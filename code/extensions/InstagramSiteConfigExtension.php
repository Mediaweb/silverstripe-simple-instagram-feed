<?php

class InstagramSiteConfigExtension extends DataExtension
{
    private static $db = [
        'InstagramClientID'     => 'Varchar(255)',
        'InstagramClientSecret' => 'Varchar(255)',
        'InstagramAccessToken'  => 'Varchar(255)',
        'NumberOfItems'         => 'Varchar(10)',
        'RandomImages'          => 'Boolean',
        'CachingTime'           => 'Varchar(10)',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            "Root.Instagram", [
            HeaderField::create('UserDetails', _t('InstagramFeed.UserDetails', 'Instagram User Details')),
            // ClientID and ClientSecret not needed at the moment, dor future use.
            //TextField::create('InstagramClientID', _t('InstagramFeed.ClientID', 'Client ID')),
            //TextField::create('InstagramClientSecret', _t('InstagramFeed.ClientSecret', 'Client Secret')),
            LiteralField::create('InstagramFeed.InfoText', 'If you don\'t have a token you can <a href="http://instagram.pixelunion.net/" target="_blank">generate one here</a>.'),
            TextField::create('InstagramAccessToken', _t('InstagramFeed.AccessToken', 'Access Token')),
            HeaderField::create('FeedDefaults', _t('InstagramFeed.FeedDefaults', 'Instagram Display Defaults')),
            TextField::create('CachingTime', _t('InstagramFeed.CachingTime', 'Feed cache (in seconds)'))
                ->setAttribute('placeholder', '600'),
            TextField::create('NumberOfItems', _t('InstagramFeed.NumberOfItems', 'Number of images to show')),
            CheckboxField::create('RandomImages', _t('InstagramFeed.RandomImages', 'Randomize images')),
        ]);
    }

}