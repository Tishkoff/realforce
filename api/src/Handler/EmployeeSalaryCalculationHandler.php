<?php

namespace App\Handler;

use App\Entity\Employee;
use App\Entity\PropertyCondition;
use App\Entity\PropertyValue;
use Exception;

/**
 * Class EmployeeSalaryCalculationHandler
 * @package App\Handler
 */
class EmployeeSalaryCalculationHandler
{
    /**
     * @param Employee $data
     * @return Employee
     * @throws Exception
     */
    public function handle(Employee $data) : Employee
    {
        $data->finalSalary = $data->salary;

        foreach ($data->propertyValues as $propertyValue) {
            $data->finalSalary += $this->handlePropertyValue($propertyValue, $data->salary);
        }

        return $data;
    }

    /**
     * Handles property value calculations.
     *
     * @param PropertyValue $propertyValue
     * @param float $salary
     * @return float
     * @throws Exception
     */
    public function handlePropertyValue(PropertyValue $propertyValue, float $salary) : float
    {
        $salaryCorrection = 0;
        $conditions = $propertyValue->property->propertyConditions;

        foreach ($conditions as $condition) {
            $salaryCorrection += $this->handleCondition($condition, $propertyValue->value, $salary);
        }

        return $salaryCorrection;
    }

    /**
     * Function handles dynamic properties and conditions.
     *
     * @param PropertyCondition $condition
     * @param string $value
     * @param float $salary
     * @return float
     * @throws Exception
     */
    public function handleCondition(PropertyCondition $condition, string $value, float $salary) : float
    {
        $salaryCorrection = 0;
        $isConditionApplicable =
            $this->handleConditionOperator($condition->condition, $value, $condition->value);

        if ($isConditionApplicable) {
            if ($condition->flatPercent) {
                $salaryCorrection += $salary * $condition->amount / 100;
            } else {
                $salaryCorrection += $condition->amount;
            }

            if ( ! $condition->debitCredit) {
                $salaryCorrection *= -1;
            }
        }

        return $salaryCorrection;
    }

    /**
     * Function handles dynamic condition from the database.
     *
     * @param string $operatorValue
     * @param string $leftSideOfComparison
     * @param string $rightSideOfComparison
     * @return bool
     * @throws Exception
     */
    private function handleConditionOperator(string $operatorValue, string $leftSideOfComparison, string $rightSideOfComparison) : bool
    {
        $returnValue = false;

        switch ($operatorValue) {
            case '==':
                $returnValue = ($leftSideOfComparison == $rightSideOfComparison);
                break;
            case '!=':
                $returnValue = ($leftSideOfComparison != $rightSideOfComparison);
                break;
            case '>=':
                $returnValue = ($leftSideOfComparison >= $rightSideOfComparison);
                break;
            case '<=':
                $returnValue = ($leftSideOfComparison <= $rightSideOfComparison);
                break;
            case '>':
                $returnValue = ($leftSideOfComparison > $rightSideOfComparison);
                break;
            case '<':
                $returnValue = ($leftSideOfComparison < $rightSideOfComparison);
                break;
            default:
                throw new Exception("Wrong '{$operatorValue}' condition operator provided.");
        }

        return $returnValue;
    }
}
