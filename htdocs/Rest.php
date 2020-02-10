<?php
abstract class ResponseTypes
{
    const succeeded = 0;
    const FatalError = 1;
    const Silent_FatalError = 2;
}

class Response
{
    public int $ResponseStatus;
    public function __construct(int $Status, $body)
    {
        $this->ResponseStatus = $Status;
        $this->StatusMessage = $body;

        if ($Status == ResponseTypes::FatalError) {
            $e = new Exception();
            $this->StackCall = $e->getTraceAsString();
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }
}

class UserLoginResponse extends Response
{
    public function __construct(int $Status,string $body ,User $user)
    {
        parent::__construct($Status,$body);
        $this->user = $user;
    }
    public function __toString()
    {
        return json_encode($this);
    }
}

class QuaryResponse extends Response
{
    public function GetDBStatusMessage(PDOStatement $db)
    {
        return $db->errno;
    }

    public function __construct(PDOStatement $db)
    {
        parent::__construct(ResponseTypes::FatalError,$this->GetDBStatusMessage($db));
    }
}

class UserRegisterQuaryResponse extends QuaryResponse
{
    public function GetDBStatusMessage(PDOStatement $db)
    {
        if ($db->errno == 1062) {
            return "UserName not available";
        }
        return "$db->errno";
    }
}

class EventSucceeded extends Response
{
    public function __construct()
    {
        parent::__construct(ResponseTypes::succeeded,"Event Executed")
    }
}

class UsersPageResponse extends Response
{
    public $UserInfo = array();
    public function __construct(PDOStatement $db)
    {
        $this->ResponseStatus = ResponseTypes::succeeded;
        $this->StatusMessage = json_encode($db->fetchAll(PDO::FETCH_OBJ));

        foreach ($db->fetchAll(PDO::FETCH_OBJ) as $element)
        {
            array_push($this->UserInfo,new user($element));
        }
    }
}


abstract class Node
{
    public string Name;
    public Location;

    public function __construct($data)
    {
        $this->Name = $data->Name;
        $this->Location = $data->Location;
    }

    public static function LoadFromJsonObject($data)
    {
        switch($data['$type'])
        {
            case InfoNode::GetType():
                return new InfoNode($data);
            break;
        }
    }

    public function GetType(){return "NULL BASENODE";}
}

class InfoNode extends Node
{

    public string Info;
    public function __construct($data)
    {
        parent::__construct($data);
        $this->Info = $data->info; 
    }
    
    public static function GetType()
    {
        return "Core.PlaySpace.InfoNode, Assembly-CSharp";
    }
}

class Location
{
    public string Image;
    public Node[] nodes; 
}

class PlaySpace
{
    public string Name;
    public string Description;
    public Location[] Locations;


    public __construct(string)

    public static function LoadFromJsonObject($data)
    {

    }
}


class User
{
    public function __construct($data)
    {
        //var_dump($data);
        $this->firstName = $data->firstname;
        $this->lastName = $data->lastname;
        $this->Klas = $data->klas;
        $this->isTeacher = $data->teacher;
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
