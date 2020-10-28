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
            // getting base price percent based on passing rule with day & hours
            $basePercent = $this->getBasePricePercent();

            // calculation of total policy amounts
            $basePrice = $this->round($this->inputData['estimated_car_value'] * ($basePercent / 100));
            $commission = $this->round($basePrice * ($this->percents['commission'] / 100));
            $tax = $this->round($basePrice * ($this->percents['tax']  / 100));

            // filling up totals
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

            // if instalments amount more than 1 we should split totals into parts
            if ($this->inputData['instalments_number'] > 1) {
                // calculation of instalments totals separated by instalments number
                $instalmentPartPrice = $this->calculateInstalmentValue($basePrice);
                $instalmentPartCommission = $this->calculateInstalmentValue($commission);
                $instalmentPartTax = $this->calculateInstalmentValue($tax);

                for ($i = 1; $i <= $this->inputData['instalments_number']; ++$i) {
                    // checking for last step
                    if ($i == $this->inputData['instalments_number']) {
                        // on a last step, we should take care about remainder, we just adding remainder
                        // to the last step of calculation if it presented, if it is not we will add zero value
                        $instalmentPartPrice =
                            $this->round($instalmentPartPrice + $basePrice - ($instalmentPartPrice * $this->inputData['instalments_number']));
                        $instalmentPartCommission =
                            $this->round($instalmentPartCommission + $commission - ($instalmentPartCommission * $this->inputData['instalments_number']));
                        $instalmentPartTax =
                            $this->round($instalmentPartTax + $tax - ($instalmentPartTax * $this->inputData['instalments_number']));
                    }

                    // put all calculated data for current step into output array
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
        $instalmentValue = $value / $this->inputData['instalments_number'];
        $remainder = $value % $this->inputData['instalments_number'];
        // if we do not have remainder it means that the current number correctly divided by the value
        // and we do not need to take care about remainder
        if ($remainder == 0)
            return $this->round($instalmentValue);

        // in other case we dividing only integer part of number, and adding remainder calculated for this step
        // to keep proportion
        return floor($instalmentValue) + $this->round($remainder / $this->inputData['instalments_number']);
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
