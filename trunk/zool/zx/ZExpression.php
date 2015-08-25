<?php

namespace zool\zx;

use zool\xml\elements\ZXmlElement;

use zool\base\ZComponent;

class ZExpression extends ZComponent
{

  // we user escape character
  const EXPRESSION_PATTERN = '/(.*)(([^\\\\])?#{(.*)})/';

  const DEAULT_VALUE_OPERATOR = '~';
  const LITERAL_PATTERN = '/[a-zA-Z_]([a-zA-Z0-9_]*/';
  const FUNCTION_PATTERN = '/(^([\\\\a-zA-Z_]([\\\\a-zA-Z0-9_]*))\((.*)\))(.*)/';
  // const LIST_PATTERN = '/(.*)(,\\s*(.*))*/';
  const VARIABLE_PATTERN = '/^([a-zA-Z_][a-zA-Z0-9_]*)$/';
  const STRING_PATTERN = "/^'(.*)'$/";
  const BINARY_OPERATOR_PATTERN = '/(.*)(\\s+)+(\\+|\\-|(\\<=)|(\\>=)|(gt)|(ge)|(lt)|(le)|\\>|\\<|(eq)|(ne)|(==)|(or)|(and)|(~)|(!=))(\\s+)?(.*)/';
  const CONCAT_STRING_PATTERN = '/(.*)(\\s+)?(\\.)(\\s+)?(.*)/';
  const ARRAY_INDEX_PATTERN = '/(.+)\[(.+)\](.*)/';
  const OBJECT_INDEX_PATTERN = '/(([a-zA-Z_][a-zA-Z0-9_]*)->)([a-zA-Z_][a-zA-Z0-9_]*)(\((.*)\))?(.*)/';
  const EMPTY_PATTERN = '/(empty)(\\s+)(.*)/';
  const NEGATE_PATTERN = '/(not)(\\s+)(.*)/';
  const CONDITIONAL_OPERATOR_PATTERN = '/(.*)\\?(.*)\\:(.*)/';

  const PRIMITIVE_TYPES = 'boolean,integer,double,string,null';
  const LOGICAL_OPERATORS = 'or,and';

  const TMP_VARIABLE_NAME = 'ZEXPRESSION_TMP_VAR';


  private $expression = '';
  private $context = null;
  private $zx = '';
  private $_value = null;


  public function __construct($expression, $context = null, $zx = '')
  {

    // TODO
    self::isEvaluatable($expression);

    $this->expression = $expression;
    $this->zx = $zx;
    $this->context = $context;
  }

  private function evaluate()
  {

    return self::evl($this);

  }

  public function getContext()
  {
    return $this->context;
  }


  public function getExpression()
  {
    return $this->expression;
  }

  public function getFullExpression()
  {
    // TODO handling whitespaces
    return '#{'.$this->expression . '}';
  }

  public function getValue()
  {

    if (is_null($this->_value)) {
      $this->_value = $this->evaluate();
    }

    return $this->_value;
  }

  protected static function evl($exprObj)
  {

    $expression = $exprObj->expression;

    $expression = trim($expression);

    if(strlen($expression) == 0) return null;

    if ($expression == 'false')
    return false;
    if ($expression == 'true')
    return true;

    if ($expression == 'null'){
      return null;
    }

    preg_match(self::NEGATE_PATTERN, $expression, $matches);
    if(!empty($matches)){
      $expr = $matches[3];

      $zexp = new ZExpression($expr, $exprObj->context);

      return !($zexp->value);
    }

    preg_match(self::EMPTY_PATTERN, $expression, $matches);
    if(!empty($matches)){
      $expr = $matches[3];

      $zexp = new ZExpression($expr, $exprObj->context);
      $value = $zexp->value;
      return empty($value);
    }

     /*
     * CONDITIONAL test
     */
    preg_match(self::CONDITIONAL_OPERATOR_PATTERN, $expression, $matches);
    if (!empty($matches)) {

      $condition = $matches[1];
      $op1 = $matches[2];
      $op2 = $matches[3];

      $condition = new ZExpression($condition, $exprObj->context);

      $result = $condition->value ? new ZExpression($op1, $exprObj->context) : new ZExpression($op2, $exprObj->context);

      return $result->value;

    }


    /*
     * STRING CONCAT test
     */
    preg_match(self::CONCAT_STRING_PATTERN, $expression, $matches);

    if (!empty($matches)) {
      try {

        $exp1 = new ZExpression($matches[1], $exprObj->context);
        $exp2 = new ZExpression($matches[5], $exprObj->context);

        $op1 = $exp1->value;
        $op2 = $exp2->value;

        // TODO cannot concat anything
        //self::ensureType($op1, 'string');
        //self::ensureType($op2, 'string');

        return $op1 . $op2;

      }
      catch (ZExpressionException $e) {
        throw new ZExpressionException("Operator must be string in $expression.", 301, $e);
      }
    }

    preg_match(self::OBJECT_INDEX_PATTERN, $expression, $matches);
    if(!empty($matches)){
      $obj = $matches[2];
      $prop = $matches[3];
      $methodCall = $matches[4];
      $methodParams = $matches[5];

      $tail = $matches[6];


      $objectExp = new ZExpression($obj, $exprObj->context);
      $objectValue = $objectExp->value;

      if(empty($methodCall)){
        if($objectValue != null)
          $propValue = $objectValue->$prop;
        else $propValue = null;
      }else{

        $params = array();
        if(!empty($methodParams)){
          $params = self::parseList($methodParams, $exprObj->context, true);
        }

        if(!$objectValue)return null;

        $propValue = call_user_func_array(array($objectValue, $prop) ,$params);
      }

      // no need more parsing
      if(empty($tail)){
        return $propValue;
      }

      // we set the the value in the context than we continue the parsing
      $exprObj->context->setContext(self::TMP_VARIABLE_NAME, $propValue);

      $newExpr = self::TMP_VARIABLE_NAME . $tail;

      // we parse onwards
      $retExp = new ZExpression($newExpr, $exprObj->context);

      $retValue = $retExp->value;

      // unset the temporaly value before returning the value.
      // this unset must stay here, after value getting.
      $exprObj->context->unsetContext(self::TMP_VARIABLE_NAME);

      return $retValue;

    }

    /*
     * LIST test
     */
    if (count($list = self::parseList($expression, $exprObj->context)) > 1) {
      return $list;
    }

    /*
     * Array, or object indexing test
     */
    preg_match(self::ARRAY_INDEX_PATTERN, $expression, $matches);
    if(!empty($matches)){

      $value = null;

      $object = $matches[1];
      $index = $matches[2];
      $tail = $matches[3];

      $objectExp = new ZExpression($object, $exprObj->context);
      $object = $objectExp->value;

      $indexExp = new ZExpression($index, $exprObj->context);
      $index = $indexExp->value;

      if(is_array($object)){
        if(!array_key_exists($index, $object)){
          throw new ZExpressionException("'$index' index does not exist in array.");
        }
        $value = $object[$index];
      }

      if(is_string($object)){
        if(!is_int($index)){
          throw new ZExpressionException("String index must be a number at '$object'.");
        }
        if(strlen($object) < $index || 0 > $index){
          throw new ZExpressionException("String index out of bounds. Index: $index.");
        }
        $value = $object[$index];
      }

      if(is_object($object)){
        $value = $object->$index;
      }


      // throw new ZExpressionException("Cannot get value from $object with index $index.");

      // Handling tail

      // no need more parsing
      if(empty($tail)){
        return $value;
      }

      // we set the the value in the context than we continue the parsing
      $exprObj->context->setContext(self::TMP_VARIABLE_NAME, $value);

      $newExpr = self::TMP_VARIABLE_NAME . $tail;

      // we parse onwards
      $retExp = new ZExpression($newExpr, $exprObj->context);

      $retValue = $retExp->value;

      // unset the temporaly value before returning the value.
      // this unset must stay here, after value getting.
      $exprObj->context->unsetContext(self::TMP_VARIABLE_NAME);

      return $retValue;

    }

    /*
     * FUNCTION test
     */
    preg_match(self::FUNCTION_PATTERN, $expression, $matches);
    // its a function
    if (!empty($matches)) {
      if (!empty($matches[5]))
      throw new ZExpressionException('Bad format of expression: ' . $expression);

      $func = $matches[2];
      if (!function_exists($func))
      throw new ZExpressionException('Function does not exists: ' . $func);

      $params = explode(',', $matches[4]);
      //var_dump($matches[4]);
      array_map('trim', $params);

      foreach ($params as $param) {
        $param = trim($param, ' ');
        $exp = new ZExpression($param, $exprObj->context);
        $param = $exp->value;
      }

      $params = self::parseList($matches[4], $exprObj->context, true);

      return call_user_func_array($func, $params);
    }

    /*
     * STRING test
     */
    preg_match(self::STRING_PATTERN, $expression, $matches);
    if (!empty($matches)) {
      return $matches[1];
    }

    if (is_numeric($expression)) {
      return 1 * $expression;
    }

    /*
     * VARIABLE test
     */
    preg_match(self::VARIABLE_PATTERN, $expression, $matches);
    if (!empty($matches)) {
      // TODO
      if ($exprObj->context instanceof ZXmlElement) {
        return $exprObj->context->resolveFromContext($matches[0], null);
      }

      return null;
    }


    /*
     * BINARY test
     */
    preg_match(self::BINARY_OPERATOR_PATTERN, $expression, $matches);
    if (!empty($matches)) {

      $op1 = new ZExpression($matches[1], $exprObj->context);
      $op1 = $op1->value;

      $op2 = new ZExpression($matches[18], $exprObj->context);
      $op2 = $op2->value;

      $operator = $matches[3];

      // if the first operand is null, the result will be the second operand
      if($operator == self::DEAULT_VALUE_OPERATOR){

        if($op1 == null) return $op2;

        return $op1;
      }

      if($op1 == null) $op1 = 'null';
      if($op2 == null) $op2 = 'null';

      switch($operator){
        case '==':
        case 'eq':
          return $op1 == $op2;
        case '!=':
        case 'ne':
          return $op1 != $op2;
        case '>':
        case 'gt':
           return $op1 > $op2;
        case '<':
        case 'lt':
          return $op1 < $op2;
        case '<=':
        case 'le':
          return $op1 <= $op2;
        case '>=':
        case 'ge':
          return $op1 >= $op2;
        case 'or':
          return $op1 || $op2;
        case 'and':
          return $op1 && $op2;
        case '-':
          return $op1 - $op2;
        case '+':
          return $op1 + $op2;

          default: throw new ZExpressionException('Cannot evaluate binary experssion: ' . $expression);

      }

    }
    throw new ZExpressionException('Cannot evaluate expression: "' . $expression . '"');

  }

  private static function isPrimitive($expr)
  {
    return in_array(gettype($expr), explode(',', self::PRIMITIVE_TYPES));
  }

  public static function parseList($expr, $context, $evalItems = false){
    $commas = 0;
    $braces = 0;
    $blocks = 0;

    $list = array();
    $tmpstring = '';

    if(strpos($expr, ",") === false){
      $list = array($expr);
    }else{
      for($i=0; $i<strlen($expr); $i++){
        $ch = $expr[$i];

        if($ch == "'" && $commas == 0){
          $commas++;
          $tmpstring .= $ch;
          for($i++; $commas != 0 && $i <strlen($expr); $i++){
            $ch = $expr[$i];
            if($ch == "'") $commas--;
            $tmpstring .= $ch;
          }
          $i--;
          continue;
        }

        // brace
        if($ch == '(' && $commas == 0){
          $braces++;
          $tmpstring .= $ch;
          for($i++; $braces != 0 && $i < strlen($expr);){
            $ch = $expr[$i++];

            if($ch == ')')$braces--;
            if($ch == '(')$braces++;

            $tmpstring .= $ch;
          }
          $i--;
          continue;
        }


        // new list item
        if($ch == ',' && $commas == 0 && $braces == 0 && $blocks == 0){
          $list[] = $tmpstring;
          $tmpstring = '';
          continue;
        }
        $tmpstring .= $ch;
      }

      $list[] = $tmpstring;

    }


    if($evalItems){
      foreach ($list as $key => $item){
        $itemExpr = new ZExpression($item, $context);
        $list[$key] = $itemExpr->value;
      }
    }

    return $list;
  }

  private static function ensureType($obj, $type)
  {
    if (gettype($obj) != $type)
    throw new ZExpressionException("$obj must have type $type.");
  }

  public static function parse($expr, $context = null)
  {
    if (empty($expr) || !is_string($expr)) {
      return false;
    }

    preg_match(self::EXPRESSION_PATTERN, $expr, $matches);
    if (empty($matches)) {
      return false;
    }

    // escaped ZX, the last character is \
    if (substr($matches[1], -1, 1) == '\\') {
      return false;
    }

    if (!empty($matches[1])) {
      // throw new ZExpressionException('Expression must start with #. Expression: ' . $expression);
    }

    return new ZExpression($matches[4], $context, $expr);
  }

  public static function unpackExpression($expr){
    preg_match(self::EXPRESSION_PATTERN, $expr, $matches);

    if (empty($matches) || substr($matches[1], -1, 1) == '\\') {
      throw new ZExpressionException('String "'.$expr.'" does not contatain ZX.');
    }

    return $matches[4];
  }

  public static function isEvaluatable($expr)
  {

  }

}
