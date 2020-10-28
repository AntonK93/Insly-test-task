<?php

require_once '../core/services/calculator.php';
require_once '../core/services/fetchTimeZoneByIp.php';

$type = isset($_POST['type']) ? $_POST['type'] : false;
if (!$type)
    return;

switch ($type) {
    case 'task-1':
        getFirstTestResult();
        break;
    case 'task-2':
        getSecondTestResult();
        break;
}

function getFirstTestResult()
{
    $names = array('name' => 'Anton');

    foreach ($names as $name)
        print $name;
}

function getSecondTestResult()
{
    $estimatedValue = $_POST['estimatedValue'];
    $taxPercentage = $_POST['taxPercentage'];
    $instalmentsNumber = $_POST['instalmentsNumber'];

    if ($calculator = new Calculator()) {
        if ($clientIpInfo = new FetchTimeZoneByIp($_SERVER['REMOTE_ADDR']))
            if ($clientTimeZone = $clientIpInfo->getClientTimeZone())
                if ($clientTimeZone != 'Undefined') {
                    $calculator->setClientTimeZone($clientTimeZone);
                    $calculator->setEstimatedValue($estimatedValue);
                    $calculator->setTaxPercent($taxPercentage);
                    $calculator->setInstalmentsNumber($instalmentsNumber);

                    if ($calculator->calculateCarInsurance())
                        return print json_encode($calculator->getCalculatedData());
                }
    }

    return print json_encode(array('message' => 'Something went wrong in calculation'));
}
