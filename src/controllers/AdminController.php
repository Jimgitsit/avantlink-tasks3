<?php

namespace AvantLink\Controller;

use Silex\Application;

class AdminController {
	public function defaultAction(Application $app) {
		$em = $app['orm.em'];
		$tasks = $em->getRepository('AvantLink\Entity\Task')->findAll();
		
		return $app['twig']->render('tasks.html.twig', array(
			'tasks' => $tasks,
		));
	}
}