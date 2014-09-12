<?php

use Illuminate\Validation\Validator;

class ValidationIterator extends Validator {

    /**
     * @param $attribute
     * @param array $ruleSet
     * @param array $messages
     * @throws \InvalidArgumentException
     */
    public function iterate($attribute, array $ruleSet = [], $messages = [])
    {
        $payload = array_merge($this->data, $this->files);

        $input = array_get($payload, $attribute);

        if (!is_array($input)) {
            throw new \InvalidArgumentException('Attribute for iterate() must be an array.');
        }

        if (!$entries = count($input)) {
            return; // Whatever you're trying to iterate, the payload doesn't have any
        }

        for ($i = 0; $i < $entries; $i++) {
            $this->addArrayValidationRules($attribute.'.'.$i.'.', $ruleSet, $messages);
        }
    }

    /**
     * @param string $attribute
     * @param array $ruleSet
     * @param array $messages
     *
     * @return void
     */
    protected function addArrayValidationRules($attribute, $ruleSet = [], $messages = [])
    {
        foreach ($ruleSet as $field => $rules)
        {
            $rules = str_replace('required_with_parent', rtrim('required_with:'.$attribute, '.'), $rules);
            $rules = is_string($rules) ? explode('|', $rules) : $rules;

            if(isset($rules['iterate']))
            {
                $rules = $this->iterateNestedRuleSet($attribute.$field, $rules);
            }

            $this->mergeRules($attribute.$field, $rules);
        }
        $this->addArrayValidationMessages($attribute, $messages);
    }

    /**
     * Add any custom messages for this ruleSet to the validator
     *
     * @param $attribute
     * @param array $messages
     *
     * @return void
     */
    protected function addArrayValidationMessages($attribute, $messages = [])
    {
        foreach ($messages as $field => $message)
        {
            $field_name = $attribute.$field;
            $messages[$field_name] = $message;
        }
        $this->setCustomMessages($messages);
    }

    /**
     * @param $attribute
     * @param $rules
     *
     * @return array
     */
    protected function iterateNestedRuleSet($attribute, $rules)
    {
        $nestedRuleSet = isset($rules['iterate']['ruleSet']) ? $rules['iterate']['ruleSet'] : [];
        $nestedMessages = isset($rules['iterate']['messages']) ? $rules['iterate']['messages'] : [];

        $this->iterate($attribute, $nestedRuleSet, $nestedMessages);

        return array_except($rules, 'iterate');
    }

}