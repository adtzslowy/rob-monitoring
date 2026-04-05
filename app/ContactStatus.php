<?php

namespace App;

enum ContactStatus: string
{
    case NEW = 'new';
    case READ = 'read';
}
