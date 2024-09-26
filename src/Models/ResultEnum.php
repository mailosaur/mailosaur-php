<?php

namespace Mailosaur\Models;


enum ResultEnum: string
{
    case Pass = "Pass";
    case Warning = "Warning";
    case Fail = "Fail";
    case Timeout = "Timeout";
}