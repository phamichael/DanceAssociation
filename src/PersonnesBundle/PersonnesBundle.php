<?php

namespace PersonnesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PersonnesBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
