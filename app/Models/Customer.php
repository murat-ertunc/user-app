<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//This is the model for the customer table
//Customers cant login to the system, they are only for storing customer information
class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', //Name of the customer (string (default length 255))
        'email', //Email of the customer (string (default length 255))
        'phone', //Phone number of the customer (string (default length 255))w
        'company', //Company of the customer (string and nullable (default length 255))
    ];
}
