<?php

namespace Renesis\MondayLogger\Monday;

class MondayConfiguration
{
    /**
     * Monday Logger configurations array
     *
     * @var $configurations
     */
    protected  $configurations = [];

    /**
     * Query variable to prepare
     * @var $query
     */
    protected $query;

    /**
     * MondayConfiguration constructor.
     */
    public function __construct()
    {
        $this->configurations = app()['config']->get('monday-logger');
    }

    /**
     * Execute query on monday.com api
     *
     * @return mixed
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function execute()
    {
        $endpoint = "https://api.monday.com/v2";
        $authToken = getenv('MONDAY_API_KEY');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = "Authorization:$authToken";

        $headers = ['Content-Type: application/json', 'User-Agent: [MondayLogger] GraphQL Client', 'Authorization: ' . $authToken];
        $data = @file_get_contents($endpoint, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => json_encode(['query' => $this->query]),
            ]
        ]));
        $tempContents = json_decode($data, true);

        return $tempContents;
    }

    /**
     * Create Board Group on monday.com
     *
     * @param $boardId
     * @return |null
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function createGroup($boardId)
    {
        $name = 'Monday Logger';

        $query = 'create_group (board_id: '.$boardId.', group_name: "'.$name.'") {id}';

        $response = $this->mutate($query)->execute();

        return $response['data']['create_item']['id'] ?? null;
    }

    /**
     * Create item on monday.com
     *
     * @param $error
     * @return $this
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function reportToMonday($error)
    {
        $query = 'create_item (board_id: '.$this->configurations['board_id'].', 
            group_id: "'.$this->configurations['group_id'].'", 
            item_name: "'.$error['message'].'") 
            {id}';

        $response = $this->mutate($query)->execute();

        $itemId = $response['data']['create_item']['id'] ?? null;

        if (!is_null($itemId)){
            $this->createUpdateInItem($itemId,$error['trace']);
        }

        return $this;
    }

    public function getGroup($boardId)
    {

    }

    /**
     * Create Update on created item on monday,com
     * @param $itemId
     * @param $trace
     * @return $this
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function createUpdateInItem($itemId, $trace)
    {
        $query = 'create_update (item_id: '.$itemId.', body: "'
            .preg_replace('/[^A-Za-z0-9 \/ () - > \-]/', '', $trace) .
            '"){id}';

        $response = $this->mutate($query)->execute();

        return $this;
    }

    /**
     * Create GraphQl Query
     *
     * @param $query
     * @return $this
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function query($query)
    {
        $this->query = '
        query {
            '.$query.'
        }';

        return $this;
    }

    /**
     * Create GraphQl mutate query
     *
     * @param $query
     * @return $this
     * @author Syed Faisal <sfkazmi0@gmail.com>
     */
    public function mutate($query)
    {
        $this->query = '
        mutation {
            '.$query.'
        }';

        return $this;
    }
}