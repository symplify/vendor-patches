<?php

namespace VendorPatches202401\Illuminate\Contracts\Database\Query;

use VendorPatches202401\Illuminate\Database\Grammar;
interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @param  \Illuminate\Database\Grammar  $grammar
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
