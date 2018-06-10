<?php
namespace Zwei\BriefDB\Database\Query;

use Zwei\BriefDB\Common\ArrayLib;

/**
 * 条件类
 *
 * Class Condition
 * @package Zwei\BriefDB\DB
 */
class Condition extends ConditionAbstract
{

    /**
     * 添加条件
     *
     * @param string $field 字段
     * @param string|integer|float $value 字段值
     * @param mixed $expressionOperator 表达式操作符(=,>=,in等)
     * @return $this
     */
    public function condition($field, $value = NULL, $expressionOperator = NULL)
    {

        if (!isset($expressionOperator)) {
            if (is_array($value)) {
                $expressionOperator = 'IN';
            }
            else {
                $expressionOperator = '=';
            }
        }
        $this->conditions[] = array(
            'field' => $field,
            'value' => $value,
            'operator' => $expressionOperator,
        );
        return $this;
    }

    /**
     * 添加复杂的条件
     *
     * @param string|Condition $snippet 小片段
     * @param array|null $args 参数
     * @return $this
     */
    public function conditionComplex($snippet, $args)
    {
        $this->conditions[] = array(
            'field' => $snippet,
            'value' => $args,
            'operator' => NULL,
        );
        return $this;
    }

    /**
     * 字段不为null
     */
    public function isNull($field) {
        return $this->condition($field, NULL, 'IS NULL');
    }

    /**
     * 字段为null
     */
    public function isNotNull($field) {
        return $this->condition($field, NULL, 'IS NOT NULL');
    }


    /**
     * 编译
     *
     * @return $this
     */
    public function compile()
    {
        $condition_fragments = array();
        $arguments = array();

        $conditions = $this->conditions;
        //没有条件直接返回null
        if ($this->count() < 1) {
            $this->conditionString = '';
            $this->arguments = [];
            return $this;
        }

        //条件操作
        $conditionOperator = $conditions['#conditionOperator'];
        unset($conditions['#conditionOperator']);
        foreach ($conditions as $condition) {

            switch (true) {
                case $condition['field'] instanceof Condition:
                    $condition['field']->compile();
                    if((string) $condition['field']){
                        $condition_fragments[] = '(' . (string) $condition['field'] . ')';
                        $arguments = ArrayLib::array_add($arguments, $condition['field']->arguments());
                    }

                    break;
                case empty($condition['operator']):
                    $condition_fragments[] = '(' . $condition['field'] . ')';
                    $arguments = ArrayLib::array_add($arguments, $condition['value']);
                    break;
                default:
                    $operator_defaults = array(
                        'prefix' => '',
                        'postfix' => '',
                        'delimiter' => '',
                        'operator' => $condition['operator'],
                        'use_value' => TRUE,
                    );
                    $operator = $this->mapConditionOperator($condition['operator']);
                    if (!isset($operator)) {
                        $operator = $this->mapConditionOperator($condition['operator']);
                    }
                    $isSingleValue = false;
                    $operator += $operator_defaults;
                    if (!$operator['delimiter']) {
                        $condition['value'] = array($condition['value']);
                        $isSingleValue = true;
                    }
                    if ($operator['use_value']) {
                        foreach ($condition['value'] as $value) {
                            $arguments[] = $value;
                        }
                    }
                    $condition_fragments[] = $isSingleValue ? sprintf(
                        "%s %s %s",
                        $condition['field'],
                        $operator['operator'],
                        implode(',', array_pad([], count($condition['value']), '?'))
                    ) : sprintf(
                        "%s %s %s %s %s",
                        $condition['field'],
                        $operator['operator'],
                        $operator['prefix'],
                        implode(',', array_pad([], count($condition['value']), '?')),
                        $operator['postfix']
                    );
                    break;
            }

        }
        $this->conditionString = implode(" $conditionOperator ", $condition_fragments);
        $this->arguments = $arguments;

        return $this;
    }


    /**
     * Gets any special processing requirements for the condition operator.
     *
     * Some condition types require special processing, such as IN, because
     * the value data they pass in is not a simple value. This is a simple
     * overridable lookup function.
     *
     * @param $operator
     *   The condition operator, such as "IN", "BETWEEN", etc. Case-sensitive.
     *
     * @return
     *   The extra handling directives for the specified operator, or NULL.
     */
    protected function mapConditionOperator($operator) {
        // $specials does not use drupal_static as its value never changes.
        static $specials = array(
            'BETWEEN' => array('delimiter' => ' AND '),
            'IN' => array('delimiter' => ', ', 'prefix' => '(', 'postfix' => ')'),
            'NOT IN' => array('delimiter' => ', ', 'prefix' => '(', 'postfix' => ')'),
            'EXISTS' => array('prefix' => '(', 'postfix' => ')'),
            'NOT EXISTS' => array('prefix' => '(', 'postfix' => ')'),
            'IS NULL' => array('use_value' => FALSE),
            'IS NOT NULL' => array('use_value' => FALSE),
            // Use backslash for escaping wildcard characters.
            'LIKE' => array('postfix' => " ESCAPE '\\\\'"),
            'NOT LIKE' => array('postfix' => " ESCAPE '\\\\'"),
            // These ones are here for performance reasons.
            '=' => array(),
            '<' => array(),
            '>' => array(),
            '>=' => array(),
            '<=' => array(),
        );
        if (isset($specials[$operator])) {
            $return = $specials[$operator];
        }
        else {
            // We need to upper case because PHP index matches are case sensitive but
            // do not need the more expensive drupal_strtoupper because SQL statements are ASCII.
            $operator = strtoupper($operator);
            $return = isset($specials[$operator]) ? $specials[$operator] : array();
        }

        $return += array('operator' => $operator);

        return $return;
    }


}