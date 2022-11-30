<?php

namespace schmauch\newsletter\models;

interface RecipientsInterface
{
    public function getDataProvider();
    
    public function getColumns();
}
