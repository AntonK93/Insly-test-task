<?php

class Calculator {

    private $inputData = [
        'estimated_car_value' => 0,
        'instalments_number' => 0,
        'client_date_timezone' => null,
    ];

    private $options =
        [
            'round_to_decimals' => 2
        ];

    private $calculatedData = [];

    private $percents =
        [
            'base' => 11,
            'non_base' => 13,
            'commission' => 17,
            'tax' => 0,
        ];

    private $basePricePercentRule =
        [
          'day' => 'Friday',
          'startHour' => 15,
          'endHour' => 20,
        ];

    /**
     * Fill estimated value param for further calculation
     * @param float $value
     */
    public function setEstimatedValue($value) {
        $this->inputData['estimated_car_value'] = $this->round($value);
    }

    /**
     * Fill client time zone value param for further calculation
     * @param float $value
     */
    public function setClientTimeZone($value) {
        $this->inputData['client_date_timezone'] = new DateTimeZone($value);
    }

    /**
     * Fill instalments number value param for further calculation
     * @param float $value
     */
    public function setInstalmentsNumber($value) {
        $this->inputData['instalments_number'] = (int)$value;
    }

    /**
     * Fill tax percent value param for further calculation
     * @param float $value
     */
    public function setTaxPercent($value) {
        $this->percents['tax'] = $this->round($value);
    }

    /**
     * Calculate car insurance based on provided data
     * @return bool
     */
    public function calculateCarInsurance() : bool
    {
        try {
            $basePercent = $this->getBasePricePercent();
            $basePrice = $this->round($this->inputData['estimated_car_value'] * ($basePercent / 100));
            $commission = $this->round($basePrice * ($this->percents['commission'] / 100));
            $tax = $this->round($basePrice * ($this->percents['tax']  / 100));

            $this->calculatedData['policy'] = [
                'value' => $this->inputData['estimated_car_value'],
                'basePrice' => $basePrice,
                'commission' => $commission,
                'tax' => $tax,
                'basePercent' => $basePercent,
                'commissionPercent' => $this->percents['commission'],
                'taxPercent' => $this->percents['tax'],
                'totalCost' => $this->round($basePrice + $commission + $tax),
            ];

            if ($this->inputData['instalments_number'] > 1) {
                $instalmentPartTotals = ['basePrice' => 0,'commission' => 0,'tax' => 0];

                $instalmentPartPrice = $this->calculateInstalmentValue($basePrice);
                $instalmentPartCommission = $this->calculateInstalmentValue($commission);
                $instalmentPartTax = $this->calculateInstalmentValue($tax);

                for ($i = 1; $i <= $this->inputData['instalments_number']; ++$i) {
                    $instalmentPartTotals['basePrice'] =
                        $this->round($instalmentPartTotals['basePrice'] + $instalmentPartPrice);
                    $instalmentPartTotals['commission'] =
                        $this->round($instalmentPartTotals['commission'] + $instalmentPartCommission);
                    $instalmentPartTotals['tax'] =
                        $this->round($instalmentPartTotals['tax'] + $instalmentPartTax);

                    if ($i == $this->inputData['instalments_number']) {
                        $instalmentPartPrice =
                            $this->round($instalmentPartPrice + $basePrice - $instalmentPartTotals['basePrice']);
                        $instalmentPartCommission =
                            $this->round($instalmentPartCommission + $commission - $instalmentPartTotals['commission']);
                        $instalmentPartTax =
                            $this->round($instalmentPartTax + $tax - $instalmentPartTotals['tax']);
                    }

                    $this->calculatedData['instalments'][$i] = [
                        'basePrice' => $instalmentPartPrice,
                        'commission' => $instalmentPartCommission,
                        'tax' => $instalmentPartTax,
                        'totalCost' =>
                            $this->round($instalmentPartPrice + $instalmentPartCommission + $instalmentPartTax)
                    ];
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Return base price percent based on current date time
     * @return float
     */
    private function getBasePricePercent() : float
    {
        $clientDateTime = new DateTime("now", $this->inputData['client_date_timezone']);

        $basePercent = $this->percents['base'];
        if ($clientDateTime->format('l') == $this->basePricePercentRule['day']
            && $this->isWithinHourRange($clientDateTime->format('H'),
                $this->basePricePercentRule['startHour'], $this->basePricePercentRule['endHour']))
        {
            $basePercent = $this->percents['non_base'];
        }

        return $basePercent;
    }

    /**
     * Return calculated instalment value for instalment part
     * @param float value
     * @return float
     */
    private function calculateInstalmentValue($value) : float
    {
        $remainder = $value % $this->inputData['instalments_number'];
        if ($remainder == 0)
            return $this->round($value/$this->inputData['instalments_number']);

        return floor($value/$this->inputData['instalments_number'])
            + $this->round($remainder/$this->inputData['instalments_number']);
    }

    /**
     * Return output price array
     * @return array
     */
    public function getCalculatedData() : array
    {
        return $this->calculatedData;
    }

    /**
     * Return boolean value how the hour fits in hour range
     * @param int hour
     * @param int startHour
     * @param int endHour
     * @return bool
     */
    private function isWithinHourRange($hour, $startHour, $endHour) : bool
    {
        return $hour >= $startHour && $hour < $endHour;
    }

    /**
     * Return rounded value
     * @param float value
     * @return float
     */
    private function round($value) : float
    {
        return round($value, $this->options['round_to_decimals']);
    }

}
