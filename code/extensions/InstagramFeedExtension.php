<?php

class InstagramFeedExtension extends DataExtension
{
    private static $db = [
        'NumberOfItems' => 'Varchar(10)',
        'RandomImages'  => 'Boolean',
        'ShowFeed'      => 'Boolean',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.InstagramFeed', [
            CheckboxField::create('ShowFeed', _t('InstagramFeed.ShowFeed', 'Show Feed')),
            TextField::create('NumberOfItems', _t('InstagramFeed.NumberOfItems', 'Number of images to show')),
            CheckboxField::create('RandomImages', _t('InstagramFeed.RandomImages', 'Randomize images')),
        ]);

    }

    public function ShowInstagramFeed($amount = 6, $type = 'Latest')
    {
        $config      = SiteConfig::current_site_config();
        $cachingtime = (intval($config->CachingTime) > 0 ? $config->CachingTime : 600);

        if (!$config->InstagramAccessToken) {
            return;
        }

        $url = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . $config->InstagramAccessToken;

        $amount = (intval($this->owner->NumberOfItems) > 0 ? $this->owner->NumberOfItems :
            (intval($config->NumberOfItems) > 0 ? $config->NumberOfItems : $amount)
        );

        $instagramArray = new ArrayList();

        // Caching
        $cache = SS_Cache::factory('InstagramFeed', 'Output', ['lifetime' => $cachingtime, 'automatic_serialization' => true]);

        if (!($data = $cache->load(md5($url)))) {
            if ($data = self::_getData($url)) {
                $cache->save($data, md5($url));
            }
        }

        if ($data && isset($data->data)) {

            $count   = count($data->data);
            $numbers = range(0, $count - 1);
            if ($this->owner->RandomImages || (!$this->owner->RandomImages && $config->RandomImages)) {
                shuffle($numbers);
            }

            for ($i = 0; $i < $amount; $i++) {
                if ($i > ($count - 1)) {
                    break;
                }

                $instagramArray[] = new ArrayData($data->data[$numbers[$i]]);

            }

            return $instagramArray;

        }

    }

    static function _getData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['content-type: application/json']);
        /** For large amounts of data use compression for better performance :
         * curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json','Accept-Encoding: gzip,deflate'));
         **/
//		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1); /* Due to an OpenSSL issue */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  /* Due to a wildcard certificate */
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_ENCODING, 1); /* If result is gzip then unzip */
        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($result = curl_exec($ch)) {
            if ($res = json_decode($result)) {
                curl_close($ch);

                return $res;
            } else {
                echo json_last_error();
                curl_close($ch);
                exit();
            }
        } else {
            echo curl_error($ch);
            curl_close($ch);
            exit();
        }
    }

}