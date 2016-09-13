<?php

namespace AvantLink\API;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AvantLink\Entity\Task;

class AvantLinkAPI {
	private $headers = array(
		'content-type' => 'application/json'
	);
	
	public function getTask(Request $req, Application $app) {
		$id = $req->get('id');
		
		$em = $app['orm.em'];
		$task = $em->getRepository('AvantLink\Entity\Task')->find($id);
		
		$serializer = $app['serializer'];
		
		if (empty($task)) {
			return new Response($serializer->serialize("Task with id $id does not exist.", 'json'), 200, $this->headers);
		}
		
		return new Response($serializer->serialize($task, 'json'), 200, $this->headers);
	}
	
	public function addTask(Request $req, Application $app) {
		$data = @json_decode($req->getContent(), true);
		
		$serializer = $app['serializer'];
		
		if (empty($data['title'])) {
			// Not sure why but sometimes the content doesn't come from getContent()
			$data['title'] = $req->request->get('title');
			if (empty($data['title'])) {
				return new Response($serializer->serialize("'title' is required.", 'json'), 400, $this->headers);
			}
		}
		
		if (strlen($data['title']) > 100) {
			return new Response($serializer->serialize("The maximum length for title is 100 characters.", 'json'), 400, $this->headers);
		}
		
		$task = new Task();
		$task->setTitle($data['title']);
		
		$em = $app['orm.em'];
		$em->persist($task);
		$em->flush();
		
		return new Response($serializer->serialize($task, 'json'), 200, $this->headers);
	}
	
	public function removeTask(Request $req, Application $app) {
		$id = $req->get('id');
		
		$em = $app['orm.em'];
		$task = $em->getRepository('AvantLink\Entity\Task')->find($id);
		
		$serializer = $app['serializer'];
		
		$msg = "Task with id $id was removed.";
		if ($task !== null) {
			$em->remove($task);
			$em->flush();
		}
		else {
			$msg = "Task with id $id does not exist.";
		}
		
		return new Response($serializer->serialize($msg, 'json'), 200, $this->headers);
	}
	
	public function getAllTasks(Request $req, Application $app) {
		$em = $app['orm.em'];
		$tasks = $em->getRepository('AvantLink\Entity\Task')->findAll();
		
		$serializer = $app['serializer'];
		
		return new Response($serializer->serialize($tasks, 'json'), 200, $this->headers);
	}
}