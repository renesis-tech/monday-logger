<?php

namespace Renesis\MondayLogger\Monday;

class MondayConfiguration
{

    protected  $configurations = [];

    public function __construct()
    {
        $this->configurations = app()['config']->get('services.renesis-monday');
    }

    public function execute($query)
    {
        $endpoint = "https://api.monday.com/v2";
        $authToken = getenv('MONDAY_API_KEY');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = "Authorization:$authToken";

        $headers = ['Content-Type: application/json', 'User-Agent: [MYTEAM] GraphQL Client', 'Authorization: ' . $authToken];
        $data = @file_get_contents($endpoint, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => json_encode(['query' => $query]),
            ]
        ]));
        $tempContents = json_decode($data, true);

        return $tempContents;
    }

    public function createGroup($boardId)
    {
        $name = 'Monday Logger';

        $query = '
            mutation {
            create_group (board_id: '.$boardId.', group_name: "'.$name.'") {
                    id
                }
            }';
        $this->execute($query);
    }

    public function createItem($error)
    {

        $query = '
            mutation {
            create_item (board_id: '.$this->configurations['board_id'].', 
            group_id: "'.$this->configurations['group_id'].'", 
            item_name: "'.$error['message'].'") 
            {id}
            }';

        $response = $this->execute($query);

        $itemId = $response['data']['create_item']['id'];

        $this->createUpdateInItem($itemId,$error['trace']);
    }

    public function getGroup($boardId)
    {

    }

    public function createUpdateInItem($itemId,$trace)
    {
        $query = '
        mutation {
            create_update (item_id: '.$itemId.', body: "'.preg_replace('/[^A-Za-z0-9 \/ () - > \-]/', '', $trace).'") 
            {id}
        }';

        $response = $this->execute($query);

        dd($response,$query);
    }
}