<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Handler\EmployeeSalaryCalculationHandler;
use Exception;

/**
 * Class CalculateEmployeeSalary
 * @package App\Controller
 */
class CalculateEmployeeSalary
{
    /**
     * @var EmployeeSalaryCalculationHandler
     */
    private $employeeSalaryCalculationHandler;

    /**
     * CalculateEmployeeSalary constructor.
     * @param EmployeeSalaryCalculationHandler $employeeSalaryCalculationHandler
     */
    public function __construct(EmployeeSalaryCalculationHandler $employeeSalaryCalculationHandler)
    {
        $this->employeeSalaryCalculationHandler = $employeeSalaryCalculationHandler;
    }

    /**
     * @param Employee $data
     * @return Employee
     * @throws Exception
     */
    public function __invoke(Employee $data): Employee
    {
        $this->employeeSalaryCalculationHandler->handle($data);

        return $data;
    }
}
