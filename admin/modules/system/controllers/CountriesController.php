<?php
class CountriesController
{
    public function select2()
    {
        select2_response('countries', ['id', 'name'], 'name', 'name', 20);
    }
}