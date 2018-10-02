<?php

namespace Rhubarb\Website\Layouts;

use Rhubarb\Leaf\LayoutProviders\LayoutProvider;
use Rhubarb\Leaf\Leaves\Leaf;

class FieldSetWithLabelsLayoutProvider extends LayoutProvider
{

    /**
     * Prints the layout surrounded by a container with a title or legend.
     *
     * @param string $containerTitle
     * @param mixed[] $items
     */
    public function printItemsWithContainer($containerTitle, ...$items)
    {
        $cssClass = "";

        if ($containerTitle != "") {
            $cssClass .= " has-legend";
        }

        ?>
        <div class="c-form <?= $cssClass ?>">
            <fieldset>
                <?php

                if ($containerTitle != "") {
                    $this->printContainerTitle($containerTitle);
                }

                $args = func_get_args();
                $args = array_slice($args, 1);

                call_user_func_array([$this, "printItems"], $args);

                ?>
            </fieldset>
        </div>
        <?php
    }


    public function printContainerTitle($containerTitle)
    {
        print '<legend class="c-form__legend">' . $containerTitle . '</legend>';
    }

    /**
     * Prints an array of label to value pairs.
     *
     * @param $pairs
     */
    public function printLabelValuePairs($pairs)
    {
        $registeredInputs = [];

        foreach ($pairs as $key => $value) {
            $label = "";
            $control = null;

            if (is_string($key)) {
                if (is_string($value)) {
                    $fieldName = $value;
                    $label = $key;

                    $control = $this->generateValue($fieldName);
                } else {
                    $control = $value;
                    $label = $key;
                }
            } else {
                $fieldName = $value;

                if (is_object($value)) {
                    $control = $value;
                } else {
                    $control = $this->generateValue($fieldName);
                }

                if (is_string($control)) {
                    $label = sizeof($registeredInputs);
                } else {
                    if (method_exists($control, "GetLabel")) {
                        $label = $control->getLabel();
                    }
                }
            }

            $registeredInputs[$label] = $control;
        }

        foreach ($registeredInputs as $label => $control) {
            if (is_numeric($label)) {
                $label = "&nbsp;";
            }

            $this->printValueWithLabel($control, $label);
        }
    }

    /**
     * Prints a single item
     *
     * @param $value
     * @param $label
     */
    public function printValueWithLabel($value, $label)
    {
        $controlName = (is_object($value) && ($value instanceof Leaf)) ? $value->getName() : "";

        ?>
        <div class="c-form__group">
            <label class="c-form__label" for="<?= $controlName; ?>"><?php $this->printLabel($label); ?></label>

            <div class="c-form__inputs">
                <?php $this->printValue($value); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Prints the content of a label for an item
     *
     * @param $label
     */
    public function printLabel($label)
    {
        print $label;
    }

    /**
     * Prints the value or presenter
     *
     * @param $value
     */
    public function printValue($value)
    {
        print $value;
    }

    /**
     * Scan the string for {} placeholders that might contain input names.
     *
     * @param $data
     * @return string
     */
    protected function parseStringAsTemplate($data)
    {
        $t = $html = $data;

        while (preg_match("/[{]([^{}]+)[}]/", $t, $regs)) {
            $t = str_replace($regs[0], "", $t);

            $input = $this->generateValue($regs[1]);

            if ($input !== null && $input !== false && $input !== $regs[1]) {
                $html = str_replace($regs[0], (string)$input, $html);
            }
        }

        return $html;
    }
}
