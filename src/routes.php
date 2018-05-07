<?php

use Slim\Http\Request;
use Slim\Http\Response;

class TicketMapper
{
    public function getTicketById($id)
    {
        return $id;
    }
}
// Routes

$app->get('/ticket/{id}', function (Request $request, Response $response, $args) {
    $ticket_id = (int) $args['id'];
    $this->logger->addinfo("Ticket list");
    $mapper = new TicketMapper();
    $tickets = $mapper->getTicketById($ticket_id);

    $response->getBody()->write(var_export($tickets, true));
    return $response;
});

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.html.twig', [
        'name' => $args['name']
    ]);
});
