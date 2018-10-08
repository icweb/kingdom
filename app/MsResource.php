<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\Group;
use Microsoft\Graph\Model\User;

class MsResource extends Model
{
    private $graph;

    public $table = 'ms_resources';

    public $fillable = [
        'ms_id',
        'type',
        'displayName',
        'description',
        'mailEnabled',
        'mailNickname',
        'manager_id',
        'accountEnabled',
        'mobilePhone',
        'mail',
        'jobTitle',
        'officeLocation',
        'department',
        'businessPhone_1',
        'businessPhone_2',
        'businessPhone_3',
        'businessPhone_4',
        'businessPhone_5',
    ];

    private function graph()
    {
        if(!$this->graph)
        {
            $graph = new Graph();
            $this->graph = $graph->setAccessToken(Token::fetch());
        }

        return $this->graph;
    }

    public function getOrCreate($ms_id, $type = 'USER')
    {
        $ms_resource = MsResource::where(['ms_id' => $ms_id, 'type' => $type])->count();

        if(!$ms_resource)
        {
            $data = ['ms_id' => $ms_id, 'type'  => $type];

            if($type === 'USER')
            {
                $user = $this
                    ->graph()
                    ->createRequest("GET", "/users/" . $ms_id)
                    ->setReturnType(User::class)
                    ->execute();

                $data['manager_id'] = $user->getManager()->getId();
                $data['accountEnabled'] = $user->getAccountEnabled();
                $data['mobilePhone'] = $user->getMobilePhone();
                $data['mail'] = $user->getMail();
                $data['jobTitle'] = $user->getJobTitle();
                $data['officeLocation'] = $user->getOfficeLocation();
                $data['department'] = $user->getDepartment();

                if(isset($user->getBusinessPhones()[0])) $data['businessPhone_1'] = $user->getBusinessPhones()[0];
                if(isset($user->getBusinessPhones()[1])) $data['businessPhone_2'] = $user->getBusinessPhones()[1];
                if(isset($user->getBusinessPhones()[2])) $data['businessPhone_3'] = $user->getBusinessPhones()[2];
                if(isset($user->getBusinessPhones()[3])) $data['businessPhone_4'] = $user->getBusinessPhones()[3];
                if(isset($user->getBusinessPhones()[4])) $data['businessPhone_5'] = $user->getBusinessPhones()[4];
            }
            else
            {
                $group = $this
                    ->graph()
                    ->createRequest("GET", "/groups/" . $ms_id)
                    ->setReturnType(Group::class)
                    ->execute();

                $data['displayName'] = $group->getDisplayName();
                $data['description'] = $group->getDescription();
                $data['mailEnabled'] = $group->getMailEnabled();
                $data['mailNickname'] = $group->getMailNickname();
            }

            $ms_resource = MsResource::create($data);
        }

        return $ms_resource;
    }
}
