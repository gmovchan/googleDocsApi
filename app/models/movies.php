<?php

class Movies
{

    private $spreadsheetId;
    private $service;

    function __construct($spreadsheetId, $service)
    {
        $this->service = $service;
        $this->spreadsheetId = $spreadsheetId;
    }

    public function getRows($range)
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();
        return $values;
    }

}

