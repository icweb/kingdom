<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\Group;
use Microsoft\Graph\Model\MailboxSettings;
use Microsoft\Graph\Model\User;
use Mpociot\Versionable\VersionableTrait;

class MsResource extends Model
{
    use VersionableTrait;

    protected $keepOldVersions = 10;

    protected $dontVersionFields = ['created_at', 'updated_at', 'onPremisesLastSyncDateTime'];

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
        'city',
        'companyName',
        'country',
        'givenName',
        'imAddresses',
        'onPremisesImmutableId',
        'passwordPolicies',
        'postalCode',
        'preferredLanguage',
        'state',
        'streetAddress',
        'surname',
        'usageLocation',
        'userPrincipalName',
        'userType',
        'aboutMe',
        'birthday',
        'hireDate',
        'interests',
        'mySite',
        'pastProjects',
        'preferredName',
        'responsibilities',
        'schools',
        'skills',
        'deviceEnrollmentLimit',
        'classification',
        'createdDateTime',
        'groupTypes',
        'renewedDateTime',
        'securityEnabled',
        'visibility',
        'allowExternalSenders',
        'autoSubscribeNewMembers',
        'isSubscribedByMail',
        'unseenCount',
        'mailNickname',
        'onPremisesLastSyncDateTime',
        'onPremisesSecurityIdentifier',
        'onPremisesSyncEnabled',
        'proxyAddresses',
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

    public function getOrCreate($ms_id, $type = 'USER', $changeType, $apiResults = false)
    {
        info('getOrCreate reached with change type ' . $changeType);

        if($changeType !== 'deleted')
        {
            info('getOrCreate passed condition !== deleted');

            $ms_resource = MsResource::where(['ms_id' => $ms_id, 'type' => $type])->get();

            $props = [
                'USER' => [
                    'url' => 'users',
//                    'query' => '?$select=displayName,accountEnabled,mobilePhone,mail,jobTitle,officeLocation,department,mailNickname',
                    'query' => '?$select=displayName,accountEnabled,mobilePhone,mail,jobTitle,officeLocation,department,mailNickname&$expand=manager',
                    'extensions' => true,
                    'class' => User::class,
                    'fields' => [
                        'displayName' => 'getDisplayName',
                        'accountEnabled' => 'getAccountEnabled',
                        'mobilePhone' => 'getMobilePhone',
                        'mail' => 'getMail',
                        'jobTitle' => 'getJobTitle',
                        'officeLocation' => 'getOfficeLocation',
                        'department' => 'getDepartment',
                        'mailNickname' => 'getMailNickname',
                    ],
                    'arrays' => [
                        [
                            'method' => 'getBusinessPhones',
                            'field' => 'businessPhone_',
                            'length' => 5
                        ]
                    ]
                ]
            ];

            $data = ['ms_id' => $ms_id, 'type'  => $type];

            try{

                info('try reached');

                $resource = $this
                    ->graph()
                    ->setApiVersion('beta')
                    ->createRequest("GET", "/" . $props[$type]['url'] . "/" . $ms_id . $props[$type]['query'])
                    ->setReturnType($props[$type]['class'])
                    ->execute();

                info($props[$type]['fields']);
                foreach($props[$type]['fields'] as $key => $val)
                {
                    info('field value: ' . $resource->$val());
                    $data[$key] = $resource->$val();
                }

                if(count($ms_resource))
                {
                    $ms_resource = $ms_resource[0];
                    $ms_resource->update($data);
                }
                else
                {
                    $ms_resource = MsResource::create($data);
                }

                if($type === 'USER')
                {
                    $mailboxSettings = $resource->getMailboxSettings();

                    if(isset($mailboxSettings))
                    {
                        MsMailboxSetting::create([
                            'resource_id'               => $ms_resource->id,
                            'externalAudience'          => $mailboxSettings->getAutomaticRepliesSetting()->getExternalAudience()->value(),
                            'externalReplyMessage'      => $mailboxSettings->getAutomaticRepliesSetting()->getExternalReplyMessage(),
                            'internalReplyMessage'      => $mailboxSettings->getAutomaticRepliesSetting()->getInternalReplyMessage(),
                            'scheduledEndDateTime'      => $mailboxSettings->getAutomaticRepliesSetting()->getScheduledEndDateTime()->getDateTime(),
                            'scheduledStartDateTime'    => $mailboxSettings->getAutomaticRepliesSetting()->getScheduledStartDateTime()->getDateTime(),
                            'status'                    => $mailboxSettings->getAutomaticRepliesSetting()->getStatus()->value(),
                        ]);
                    }
                }

                return MsResource::findOrFail($ms_resource->id);

            } catch(\Exception $e) {
                info('exception occured');
                info([$e]);
            }



//            foreach($props[$type]['arrays'] as $array)
//            {
//                for($x = 0; $x < $array['length']; $x++)
//                {
//                    if(isset($resource->$array['method']()[$x]))
//                    {
//                        $data['businessPhone_' . ($x + 1)] = $resource->$array['method']()[$x];
//                    }
//                }
//            }

//            if($type === 'USER')
//            {
//                $user = $this
//                    ->graph()
//                    ->createRequest("GET", "/users/" . $ms_id . '?$select=displayName,accountEnabled,mobilePhone,mail,jobTitle,officeLocation,department,mailNickname,mailboxSettings')
//                    ->setReturnType(User::class)
//                    ->execute();
//
//                $data['displayName'] = $user->getDisplayName();
//               // $data['manager_id'] = $user->getManager()->getId();
//                $data['accountEnabled'] = $user->getAccountEnabled() ? 'Yes' : 'No';
//                $data['mobilePhone'] = $user->getMobilePhone();
//                $data['mail'] = $user->getMail();
//                $data['jobTitle'] = $user->getJobTitle();
//                $data['officeLocation'] = $user->getOfficeLocation();
//                $data['department'] = $user->getDepartment();
//                $data['mailNickname'] = $user->getMailNickname();
////            $data['onPremisesLastSyncDateTime'] = $user->getOnPremisesLastSyncDateTime();
////            $data['onPremisesSecurityIdentifier'] = $user->getOnPremisesSecurityIdentifier();
////            $data['onPremisesSyncEnabled'] = $user->getOnPremisesSyncEnabled();
////            $data['proxyAddresses'] = $user->getProxyAddresses();
////            $data['city'] = $user->getCity();
////            $data['companyName'] = $user->getCompanyName();
////            $data['country'] = $user->getCountry();
////            $data['givenName'] = $user->getGivenName();
////            $data['imAddresses'] = $user->getImAddresses();
////            $data['onPremisesImmutableId'] = $user->getOnPremisesImmutableId();
////            $data['passwordPolicies'] = $user->getPasswordPolicies();
////            $data['postalCode'] = $user->getPostalCode();
////            $data['preferredLanguage'] = $user->getPreferredLanguage();
////            $data['state'] = $user->getState();
////            $data['streetAddress'] = $user->getStreetAddress();
////            $data['surname'] = $user->getSurname();
////            $data['usageLocation'] = $user->getUsageLocation();
////            $data['userPrincipalName'] = $user->getUserPrincipalName();
////            $data['userType'] = $user->getUserType();
////            $data['aboutMe'] = $user->getAboutMe();
////            $data['birthday'] = $user->getBirthday();
////            $data['hireDate'] = $user->getHireDate();
////            $data['interests'] = $user->getInterests();
////            $data['mySite'] = $user->getMySite();
////            $data['pastProjects'] = $user->getPastProjects();
////            $data['preferredName'] = $user->getPreferredName();
////            $data['responsibilities'] = $user->getResponsibilities();
////            $data['schools'] = $user->getSchools();
////            $data['skills'] = $user->getSkills();
////            $data['deviceEnrollmentLimit'] = $user->getDeviceEnrollmentLimit();
//
//                if(isset($user->getBusinessPhones()[0])) $data['businessPhone_1'] = $user->getBusinessPhones()[0];
//                if(isset($user->getBusinessPhones()[1])) $data['businessPhone_2'] = $user->getBusinessPhones()[1];
//                if(isset($user->getBusinessPhones()[2])) $data['businessPhone_3'] = $user->getBusinessPhones()[2];
//                if(isset($user->getBusinessPhones()[3])) $data['businessPhone_4'] = $user->getBusinessPhones()[3];
//                if(isset($user->getBusinessPhones()[4])) $data['businessPhone_5'] = $user->getBusinessPhones()[4];
//            }
//            else
//            {
//                $group = $this
//                    ->graph()
//                    ->createRequest("GET", "/groups/" . $ms_id)
//                    ->setReturnType(Group::class)
//                    ->execute();
//
//                $data['displayName'] = $group->getDisplayName();
//                $data['description'] = $group->getDescription();
//                $data['mailEnabled'] = $group->getMailEnabled();
//                $data['mailNickname'] = $group->getMailNickname();
//                $data['mail'] = $group->getMail();
//                $data['classification'] = $group->getClassification();
//                $data['createdDateTime'] = $group->getCreatedDateTime()->format('Y-m-d H:i:s');
//                $data['groupTypes'] = implode(',', $group->getGroupTypes());
//                $data['onPremisesLastSyncDateTime'] = $group->getOnPremisesLastSyncDateTime()->format('Y-m-d H:i:s');
//                $data['onPremisesSecurityIdentifier'] = $group->getOnPremisesSecurityIdentifier();
//                $data['onPremisesSyncEnabled'] = $group->getOnPremisesSyncEnabled() ? 'Yes' : 'No';
//                $data['proxyAddresses'] = implode(',', $group->getProxyAddresses());
//                $data['renewedDateTime'] = $group->getRenewedDateTime()->format('Y-m-d H:i:s');
//                $data['securityEnabled'] = $group->getSecurityEnabled() ? 'Yes' : 'No';
//                $data['visibility'] = $group->getVisibility();
//                $data['allowExternalSenders'] = $group->getAllowExternalSenders() ? 'Yes' : 'No';
//                $data['autoSubscribeNewMembers'] = $group->getAutoSubscribeNewMembers() ? 'Yes' : 'No';
//                $data['isSubscribedByMail'] = $group->getIsSubscribedByMail() ? 'Yes' : 'No';
//                $data['unseenCount'] = $group->getUnseenCount();
//            }



        }
        else
        {
            return (object)['id' => 0];
        }
    }

    public function populateDirectory()
    {
        $graph = new Graph();
        $graph = $graph->setAccessToken(Token::fetch());

        $resources = [
            'users'  => User::class,
            'groups' => Group::class,
//            [
//                'name' => 'users',
//                'type' => 'USER',
//                'class' => User::class,
//                'query' => ''
//            ]
        ];

        foreach($resources as $key => $val)
        {
            $iterator = $graph->createCollectionRequest("GET", '/' . $key)
                ->setReturnType($val)
                ->setPageSize(999);

            while (!$iterator->isEnd())
            {
                foreach($iterator->getPage() as $item)
                {
                    $resource = new MsResource();
                    $resource->getOrCreate($item->getId(), substr(strtoupper($key), 0, -1), 'updated');
                }
            }
        }
    }

    public function mailboxSettings()
    {
        return $this->hasOne(MailboxSettings::class, 'resource_id', 'id');
    }
}
