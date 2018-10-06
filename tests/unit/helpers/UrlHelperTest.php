<?php

namespace craftunit\helpers;


use Codeception\Test\Unit;
use craft\db\MigrationManager;
use craft\fields\Url;
use craft\helpers\MigrationHelper;
use craft\helpers\UrlHelper;

/**
 * Unit tests for the Url Helper class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @author Global Network Group | Giel Tettelaar <giel@yellowflash.net>
 * @since 3.0
 */
class UrlHelperTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $entryScript;
    protected $entryUrl;
    protected $baseForUrlCreation;
    protected $cpTrigger;

    protected function _before()
    {
        $this->cpTrigger = \Craft::$app->getConfig()->getGeneral()->cpTrigger;
        $yii2 = $this->getModule('Yii2');
        $this->entryScript = $yii2->_getConfig('entryScript');
        $this->entryUrl = $yii2->_getConfig('entryUrl');

        if (\Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->baseForUrlCreation = $this->entryScript;
        } else{
            $this->baseForUrlCreation = '/';
        }
    }

    protected function _after()
    {
    }

    const ABSOLUTE_URL = 'http://craftcms.com/';
    const ABSOLUTE_URL_HTTPS = 'https://craftcms.com/';
    const ABSOLUTE_URL_WWW = 'http://www.craftcms.com/';
    const ABSOLUTE_URL_HTTPS_WWW = 'https://www.craftcms.com/';
    const NON_ABSOLUTE_URL = 'craftcms.com/';
    const NON_ABSOLUTE_URL_WWW = 'www.craftcms.com/';
    const PROTOCOL_RELATIVE_URL = '//craftcms.com/';

    /**
     * @dataProvider protocolRelativeUrlData
     * @dataProvider absoluteUrlData
     * @dataProvider fulUrlData
     */
    public function testIsUrlFunction($url, bool $result, $method)
    {
        $urlHelperResult = UrlHelper::$method($url);
        $this->assertSame($urlHelperResult, $result);
        $this->assertInternalType('boolean', $urlHelperResult);
    }

    /**
     * @return array
     */
    public function absoluteUrlData()
    {
        return [
            'absolute-url' => [ self::ABSOLUTE_URL, true, 'isAbsoluteUrl' ],
            'absolute-url-https' => [ self::ABSOLUTE_URL_HTTPS, true, 'isAbsoluteUrl' ],
            'absolute-url-https-www' => [ self::ABSOLUTE_URL_HTTPS_WWW, true, 'isAbsoluteUrl' ],
            'absolute-url-www' => [ self::ABSOLUTE_URL_WWW, true, 'isAbsoluteUrl' ],
            'non-url' => [self::NON_ABSOLUTE_URL, false, 'isAbsoluteUrl'],
            'non-absolute-url-www' => [ self::NON_ABSOLUTE_URL_WWW, false, 'isAbsoluteUrl' ]
        ];
    }

    /**
     * @return array
     */
    public function fulUrlData()
    {
        return [
            'absolute-url' => [ self::ABSOLUTE_URL, true, 'isFullUrl' ],
            'absolute-url-https' => [ self::ABSOLUTE_URL_HTTPS, true, 'isFullUrl' ],
            'absolute-url-https-www' => [ self::ABSOLUTE_URL_HTTPS_WWW, true, 'isFullUrl' ],
            'absolute-url-www' => [ self::ABSOLUTE_URL_WWW, true, 'isFullUrl' ],
            'root-relative' => [ '/22', true, 'isFullUrl' ],
            'protocol-relative' => [ self::PROTOCOL_RELATIVE_URL, true, 'isFullUrl' ],
            'mb4-string' => [ '😀😘', false, 'isFullUrl' ],
            'random-chars' => [ '!@#$%^&*()<>', false, 'isFullUrl' ],
            'random-string' => ['hello', false, 'isFullUrl'],
            'non-url' => [self::NON_ABSOLUTE_URL, false, 'isFullUrl'],
            'non-absolute-url-www' => [ self::NON_ABSOLUTE_URL_WWW, false, 'isFullUrl' ],
        ];
    }

    /**
     * @return array
     */
    public function protocolRelativeUrlData()
    {
        return [
            'root-relative-true' => [ '/22', true, 'isRootRelativeUrl'],
            'protocol-relative' => [ '//cdn.craftcms.com/22', false, 'isRootRelativeUrl' ],
            'absolute-url-https-www' => [ self::ABSOLUTE_URL_HTTPS_WWW, false, 'isRootRelativeUrl' ]
        ];
    }

    /**
     * @dataProvider urlWithParamsData()
     */
    public function testUrlWithParams($result, $url, $params)
    {
        $this->assertSame($result, UrlHelper::urlWithParams($url, $params));
    }

    public function urlWithParamsData()
    {
        return [
            'basic-array' => [
                self::ABSOLUTE_URL_HTTPS_WWW.'?param1=name&param2=name2',
                self::ABSOLUTE_URL_HTTPS_WWW,
                ['param1' => 'name', 'param2' => 'name2']
            ],
            'empty-array' => [
                self::ABSOLUTE_URL_HTTPS_WWW,
                self::ABSOLUTE_URL_HTTPS_WWW,
               []
            ],
            '4-spaces' => [
                self::ABSOLUTE_URL_HTTPS_WWW.'?    ',
                self::ABSOLUTE_URL_HTTPS_WWW,
                '    '
            ],
            'numerical-index-array'  => [
                self::ABSOLUTE_URL_HTTPS_WWW.'?0=someparam',
                self::ABSOLUTE_URL_HTTPS_WWW,
                ['someparam']
            ],
            'query-string' => [
                self::ABSOLUTE_URL_HTTPS_WWW.'?param1=name&param2=name2',
                self::ABSOLUTE_URL_HTTPS_WWW,
                '?param1=name&param2=name2'
            ],
            'pre-queried-url' => [
                self::ABSOLUTE_URL_HTTPS_WWW.'?param3=name3&param1=name&param2=name2',
                self::ABSOLUTE_URL_HTTPS_WWW.'?param3=name3',
                '?param1=name&param2=name2'
            ],
    ];
    }


    /**
     * @dataProvider cpUrlCreationData
     */
    public function testCpUrlCreation($result, $inputUrl, $params, $scheme = 'https')
    {
        $this->assertSame(
            $this->baseForUrlCreation.'?p='.$this->cpTrigger.''.$result.'',
            UrlHelper::cpUrl($inputUrl, $params, $scheme)
        );
    }

    /**
     * @return array
     */
    public function cpUrlCreationData()
    {
        return [
            'test-empty' => ['', '', []],
            'test-simple-endpoint' => [
                '/nav&param1=entry1&param2=entry2',
                'nav',
                ['param1' => 'entry1', 'param2' => 'entry2']
            ],
            'test-preexisting-endpoints' => [
                '/nav&param3=entry3&param1=entry1&param2=entry2',
                'nav?param3=entry3',
                ['param1' => 'entry1', 'param2' => 'entry2']
            ],
            [
                '/nav&param1=entry1&param2=entry2',
                'nav',
                [
                    'param1' => 'entry1',
                    'param2' => 'entry2'],
                'https'
            ],
            'test-url-gets-ignored' => [
                '/https://test.craftcms.dev&param1=entry1&param2=entry2',
                'https://test.craftcms.dev',
                ['param1' => 'entry1', 'param2' => 'entry2'],
                'https'
            ]
        ];
    }

    /**
     * @dataProvider urlWithSchemeProvider
     * @dataProvider urlWithTokenProvider
     * @dataProvider urlWithParamsProvider
     * @dataProvider stripQueryStringProvider
     * @param      $url
     * @param      $data
     * @param bool $result
     * @param      $method
     */
    public function testUrlModifiers($result, $url, $modifier, $method)
    {
        $this->assertSame($result, UrlHelper::$method($url, $modifier));
    }

    public function stripQueryStringProvider()
    {
        return [
            [
                self::ABSOLUTE_URL_HTTPS_WWW,
                self::ABSOLUTE_URL_HTTPS_WWW,
                null,
                'stripQueryString'
            ],
            [
                self::ABSOLUTE_URL_WWW,
                self::ABSOLUTE_URL_WWW.'?param1=entry1',
                null,
                'stripQueryString'
            ],
            [
            self::ABSOLUTE_URL_HTTPS_WWW,
                self::ABSOLUTE_URL_HTTPS_WWW.'?param1=entry1?param2=entry2',
                null,
                'stripQueryString'
            ]

        ];
    }
    public function urlWithParamsProvider()
    {
        return [
            [
                self::ABSOLUTE_URL_HTTPS.'?param1=entry1',
                self::ABSOLUTE_URL_HTTPS,
                ['param1' => 'entry1'],
                'urlWithParams'
            ],
            [
                self::ABSOLUTE_URL_HTTPS.'?param1=entry1&param2=entry2',
                self::ABSOLUTE_URL_HTTPS,
                ['param1' => 'entry1', 'param2' => 'entry2'],
                'urlWithParams'
            ],
            [
                self::ABSOLUTE_URL_HTTPS.'?param1=entry1&param2=entry2',
                self::ABSOLUTE_URL_HTTPS,
                'param1=entry1&param2=entry2',
                'urlWithParams'
            ],
            'anchor-gets-kept' => [
                self::ABSOLUTE_URL_HTTPS.'#anchor?param1=entry1&param2=entry2',
                self::ABSOLUTE_URL_HTTPS.'#anchor',
                'param1=entry1&param2=entry2',
                'urlWithParams'
            ],
            'prev-param-gets-kept' => [
                self::ABSOLUTE_URL_WWW.'#anchor?param3=entry3&param1=entry1&param2=entry2',
                self::ABSOLUTE_URL_WWW.'#anchor?param3=entry3',
                '?param1=entry1&param2=entry2',
                'urlWithParams'
            ],
        ];
    }
    public function urlWithTokenProvider()
    {
        return [
            [
                self::ABSOLUTE_URL.'?token=value',
                self::ABSOLUTE_URL,
                'value',
                'urlWithToken'
            ],
            [
                self::ABSOLUTE_URL.'?token=value&token=value',
                self::ABSOLUTE_URL.'?token=value',
                'value',
                'urlWithToken'
            ],
            [
                self::ABSOLUTE_URL.'?token=',
                self::ABSOLUTE_URL.'',
                '',
                'urlWithToken'
            ]
        ];
    }

    public function urlWithSchemeProvider()
    {
        return [
                [
                    str_replace('https://', 'php://', self::ABSOLUTE_URL_HTTPS_WWW),
                    self::ABSOLUTE_URL_HTTPS_WWW,
                    'php',
                    'urlWithScheme'
                ],
                [
                    str_replace('https://', 'ftp://', self::ABSOLUTE_URL_HTTPS),
                    self::ABSOLUTE_URL_HTTPS,
                    'ftp',
                    'urlWithScheme'
                ],
                [
                    str_replace('http://', 'walawalabingbang://', self::ABSOLUTE_URL),
                    self::ABSOLUTE_URL,
                    'walawalabingbang',
                    'urlWithScheme'
                ],
                [
                    self::ABSOLUTE_URL_HTTPS_WWW,
                    self::ABSOLUTE_URL_HTTPS_WWW,
                    'https',
                    'urlWithScheme'
                ],
                [
                    str_replace('http://', 'https://', self::ABSOLUTE_URL),
                    self::ABSOLUTE_URL,
                    'https',
                    'urlWithScheme'
                ],
                [
                    str_replace('https://', 'sftp://', self::ABSOLUTE_URL_HTTPS_WWW),
                    self::ABSOLUTE_URL_HTTPS_WWW,
                    'sftp',
                    'urlWithScheme'
            ],
        ];
    }

    public function testBaseTesting()
    {
        // TODO: Is this in all scenarious.
        $this->assertSame('/', UrlHelper::baseUrl());
        $this->assertSame('/', UrlHelper::baseCpUrl());
        $this->assertSame('/', UrlHelper::baseSiteUrl());
        $this->assertSame('/', UrlHelper::baseRequestUrl());
        $this->assertSame('', UrlHelper::host());
        $this->assertSame('', UrlHelper::cpHost());
    }

    public function testHostInfoRetrieval()
    {
        $this->assertSame('https://google.com', UrlHelper::hostInfo('https://google.com'));
        $this->assertSame('http://facebook.com', UrlHelper::hostInfo('http://facebook.com'));
        $this->assertSame('ftp://www.craftcms.com', UrlHelper::hostInfo('ftp://www.craftcms.com/why/craft/is/cool/'));
        $this->assertSame('walawalabingbang://gt.com', UrlHelper::hostInfo('walawalabingbang://gt.com/'));
        $this->assertSame('sftp://volkswagen', UrlHelper::hostInfo('sftp://volkswagen/2/2/2/2/2/2/2/2/2///222////222'));

        // If nothing is passed to the hostInfo() your mileage may vary depending on request type. So we need to know what to expect before hand..
        $expectedValue = \Craft::$app->getRequest()->getIsConsoleRequest() ? '' : \Craft::$app->getRequest()->getHostInfo();
        $this->assertSame($expectedValue, UrlHelper::hostInfo(''));
    }

    /**
     * @throws \craft\errors\SiteNotFoundException
     */
    public function testUrlCreation()
    {
        $siteUrl = \Craft::$app->getConfig()->getGeneral()->siteUrl;
        $cpTrigger = \Craft::$app->getConfig()->getGeneral()->cpTrigger;

        $ftpUrl = UrlHelper::urlWithScheme($siteUrl, 'ftp');

        $this->assertEquals(
            $ftpUrl.'v1/api?param1=name',
            UrlHelper::url($siteUrl.'v1/api', ['param1' => 'name'], 'ftp', false)
        );
    }

    /**
     * @return bool
     * @throws \craft\errors\SiteNotFoundException
     */
    public function testGetSchemeForTokenUrl()
    {
        $this->assertTrue(in_array(UrlHelper::getSchemeForTokenizedUrl(), ['http', 'https']));

        // Run down the logic to see what we will need to require.
        $useSslOnTokenizedUrls = \Craft::$app->getConfig()->getGeneral()->useSslOnTokenizedUrls;

        // If they've explicitly set `useSslOnTokenizedUrls` to true, require https.
        if ($useSslOnTokenizedUrls === true) {
            $this->assertSame('https', UrlHelper::getSchemeForTokenizedUrl());
            return true;
        }

        // If they've explicitly set `useSslOnTokenizedUrls` to false, require http.
        if ($useSslOnTokenizedUrls === false) {
            $this->assertSame('http', UrlHelper::getSchemeForTokenizedUrl());
            return true;
        }

        // If the siteUrl is https or the current request is https, require https://.
        $scheme = parse_url(UrlHelper::baseSiteUrl(), PHP_URL_SCHEME);
        $request = \Craft::$app->getRequest();
        if (($scheme !== false && strtolower($scheme) === 'https') || (!$request->getIsConsoleRequest() && $request->getIsSecureConnection())) {
            $this->assertSame('https', UrlHelper::getSchemeForTokenizedUrl());
            return true;
        }

        $this->assertSame('http', UrlHelper::getSchemeForTokenizedUrl());
        return true;
    }


}