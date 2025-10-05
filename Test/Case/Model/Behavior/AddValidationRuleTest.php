<?php

App::import('Component', 'Security');
App::import('Component', 'Auth');

/**
 * Base model that to load AddValidationRule behavior on every test model.
 *
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class ValidationRule extends CakeTestModel
{
    /**
     * Name for this model
     *
     * @var string
     */
    public $name = 'ValidationRule';

    /**
     * Behaviors for this model
     *
     * @var array
     */
    public $actsAs = ['Cakeplus.AddValidationRule'];

    public $validate = [
        'valuediff' => [
            'rule1' => [
                'rule' => ['compare2fields', 'valuediff_conf'],
                'message' => '【メールアドレス】 と【メールアドレス(確認)】の内容が異なります',
            ],
        ],
        'password' => [
            'rule1' => [
                'rule' => ['compare2fields', 'password_conf', true],
                'message' => 'パスワード と パスワード(確認)の内容が異なります',
            ],
        ],
        'spaceonly' => [
            'rule5' => [
                'rule' => ['spaceOnly'],
                'message' => 'スペース以外も入力してください',
            ],
        ],
        'alphanumber' => [
            'rule7' => [
                'rule' => ['alphaNumber'],
                'message' => '英数字のみで入力してください',
            ],
        ],
        'maxlengthjp' => [
            'rule2' => [
                'rule' => ['maxLengthJP', 10],
                'message' => '10文字以内です',
            ],
        ],
        'minlengthjp' => [
            'rule3' => [
                'rule' => ['minLengthJP', 2],
                'message' => '2文字以上です',
            ],
        ],
        'katakanaonly' => [
            'rule6' => [
                'rule' => ['katakanaOnly'],
                'message' => 'カタカナのみ入力してください',
            ],
        ],
        'betweenJP' => [
            'rule7' => [
                'rule' => ['betweenJP', 5, 10],
                'message' => '5文字以上10文字以内です',
            ],
        ],
        'hiraganaOnly' => [
            'rule8' => [
                'rule' => ['hiraganaOnly'],
                'message' => 'ひらがなのみ入力してください',
            ],
        ],
        'zenkakuOnly' => [
            'rule9' => [
                'rule' => ['zenkakuOnly'],
                'message' => '全角のみ入力してください',
            ],
        ],
        'telFaxJp' => [
            'rule10' => ['rule' => ['telFaxJp'],
                'message' => '正しい電話番号を入力してください',
            ],
        ],
        'mobileEmailJp' => [
            'rule11' => [
                'rule' => ['mobileEmailJp'],
                'message' => '正しい携帯メールアドレスを入力して下さい',
            ],
        ],
        'passwordValid' => [
            'rule12' => [
                'rule' => ['passwordValid','password_conf', 5, 10],
                'message' => '正しいパスワードを入力して下さい',
            ],
        ],
        'datetime_valid' => [
            'rule13' => [
                'rule' => ['datetime', 'ymd', null],
                'message' => '正しい日付を入力して下さい',
            ],
        ],
    ];
}

class AddValidationRuleTestCase extends CakeTestCase
{
    /**
     * @var ValidationRule
     */
    public $ValidationRule = null;

    public $fixtures = ['plugin.cakeplus.validation_rule'];

    public function setUp(): void
    {
        $this->ValidationRule = ClassRegistry::init('ValidationRule');
    }

    /**
     * 複数のテストをまとめて実行するメソッド
     * 失敗ケースの値と、成功ケースの値をそれぞれ配列でセットする
     *
     * @param string $field
     * @param array $setFailData
     * @param array $setSuccessData
     * @return void
     */
    private function _failSuccessTest($field, $setFailData = [], $setSuccessData = []): void
    {
        // 失敗パターン
        $data = [];
        foreach ($setFailData as $value) {
            $data['ValidationRule'][$field] = $value;
            $this->assertIdentical($this->ValidationRule->create($data), $data);
            $this->assertFalse($this->ValidationRule->validates());
            $this->assertTrue(array_key_exists($field, $this->ValidationRule->validationErrors));
        }

        // 成功パターン
        $data = [];
        foreach ($setSuccessData as $value) {
            $data['ValidationRule'][$field] = $value;
            $this->assertIdentical($this->ValidationRule->create($data), $data);
            $this->assertTrue($this->ValidationRule->validates());
            $this->assertFalse(array_key_exists($field, $this->ValidationRule->validationErrors));
        }
    }

    /**
     * 全てバリデーションに引っかかるテスト
     *
     * @return void
     */
    public function testValidataionAllFail(): void
    {
        $data = [
            'ValidationRule' => [
                'valuediff' => 'a',
                'valuediff_conf' => 's',
                'spaceonly' => ' 　',
                'alphanumber' => 'あ',
                'maxlengthjp' => 'あああああああああああ',
                'minlengthjp' => 'あ',
                'katakanaonly' => 'あ',
                'betweenJP' => 'あいうえおかきくけこさしすせそ',
                'hiraganaOnly' => 'カタカナ',
                'zenkakuOnly' => '090abc',
                'telFaxJp' => 'abcde',
                'mobileEmailJp' => 'aaaaaaa',
                'passwordValid' => 'aa',
                'password_conf' => 'aa',
            ],
        ];

        $this->assertIdentical($this->ValidationRule->create($data), $data);

        $this->assertFalse($this->ValidationRule->validates());

        $this->assertTrue(array_key_exists('valuediff', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('spaceonly', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('alphanumber', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('maxlengthjp', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('minlengthjp', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('katakanaonly', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('betweenJP', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('hiraganaOnly', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('zenkakuOnly', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('telFaxJp', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('mobileEmailJp', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('passwordValid', $this->ValidationRule->validationErrors));
    }

    /**
     * 全てバリデーションで成功するテスト
     *
     * @return void
     */
    public function testValidataionAllSuccess(): void
    {
        $data = [
            'ValidationRule' => [
                'valuediff' => 'あいうえお',
                'valuediff_conf' => 'あいうえお',
                'spaceonly' => ' 　ええ',
                'alphanumber' => 'onlyAlpharNumeric123456789',
                'maxlengthjp' => '10ああああああああ',
                'minlengthjp' => 'あa',
                'katakanaonly' => 'カタカナノミァィゥェォー゛゜',
                'betweenJP' => 'あいうえおかきくけこ',
                'hiraganaOnly' => 'ひらがな',
                'zenkakuOnly' => '全角のみです',
                'telFaxJp' => '03-1111-2222',
                'mobileEmailJp' => 'hoge..aa@softbank.ne.jp',
                'passwordValid' => 'hoge1245',
                'password_conf' => 'hoge1245',
            ],
        ];

        $this->assertIdentical($this->ValidationRule->create($data), $data);
        $this->assertTrue($this->ValidationRule->validates());

        $this->assertFalse(array_key_exists('valuediff', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('spaceonly', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('alphanumber', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('maxlengthjp', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('minlengthjp', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('katakanaonly', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('betweenJP', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('hiraganaOnly', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('zenkakuOnly', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('telFaxJp', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('mobileEmailJp', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('passwordValid', $this->ValidationRule->validationErrors));
    }

    /**
     * spaceonly, alphanum, katakanaonlyフィールドのみバリデーションに引っかかるテスト
     *
     * @return void
     */
    public function testValidataionSpaceonlyAlphanumKatakanaonlyFail(): void
    {
        $data = [
            'ValidationRule' => [
                'valuediff' => 'abcdefg 12345',
                'valuediff_conf' => 'abcdefg 12345',
                'spaceonly' => '　',
                'alphanumber' => 'only AlpharNumeric 123456789',
                'maxlengthjp' => '1234567abc',
                'minlengthjp' => 'ab',
                'katakanaonly' => 'ﾊﾝｶｸｶﾅ',
            ],
        ];

        $this->assertIdentical($this->ValidationRule->create($data), $data);
        $this->assertFalse($this->ValidationRule->validates());

        $this->assertFalse(array_key_exists('valuediff', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('spaceonly', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('alphanumber', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('maxlengthjp', $this->ValidationRule->validationErrors));
        $this->assertFalse(array_key_exists('minlengthjp', $this->ValidationRule->validationErrors));
        $this->assertTrue(array_key_exists('katakanaonly', $this->ValidationRule->validationErrors));
    }

    /**
     * Authコンポーネント系テスト
     *
     * @return void
     */
    public function testAuthHash(): void
    {
        // passwordフィールドがハッシュ化されなかった場合はエラー
        $data = [
            'ValidationRule' => [
                'password' => 'abc123',
                'password_conf' => 'abc123',
            ],
        ];
        $this->assertIdentical($this->ValidationRule->create($data), $data);
        $this->assertFalse($this->ValidationRule->validates());
        $this->assertTrue(array_key_exists('password', $this->ValidationRule->validationErrors));

        // AuthComponent::passwordを使ってハッシュ化　同一値でバリデーションエラーがないことを確認
        $data = [
            'ValidationRule' => [
                'password' => AuthComponent::password('abc123cvb'),
                'password_conf' => 'abc123cvb',
            ],
        ];
        $this->assertIdentical($this->ValidationRule->create($data), $data);
        $this->assertTrue($this->ValidationRule->validates());
        $this->assertFalse(array_key_exists('password', $this->ValidationRule->validationErrors));

        // AuthComponent::passwordを使ってハッシュ化　異なる値でバリデーションエラーに引っかかるテスト
        $data = [
            'ValidationRule' => [
                'password' => AuthComponent::password('abc123cvb'),
                'password_conf' => 'hoge111',
            ],
        ];
        $this->assertIdentical($this->ValidationRule->create($data), $data);
        $this->assertFalse($this->ValidationRule->validates());
        $this->assertTrue(array_key_exists('password', $this->ValidationRule->validationErrors));
    }

    /**
     * betweenJP テスト
     *
     * @return void
     */
    public function testValidataionBetweenJP(): void
    {
        $setFailData = ['ああ','abあい', 'aabbccddええおお' ];
        $setSuccessData = ['abcde', 'aabbccddええ', '1122334'];

        $field = 'betweenJP';

        $this->_failSuccessTest($field, $setFailData, $setSuccessData);
    }

    /**
     * hiraganaOnly テスト
     *
     * @return void
     */
    public function testValidataionHiraganaOnly(): void
    {
        $setFailData = ['あカナ','abあい', '0011ええおお','漢字も' ];
        $setSuccessData = ['がぎぁ', 'たーいへーいよー', 'にゃぴょにょ'];

        $field = 'hiraganaOnly';

        $this->_failSuccessTest($field, $setFailData, $setSuccessData);
    }

    /**
     * zenkakuOnly テスト
     *
     * @return void
     */
    public function testValidataionZenkakuOnly(): void
    {
        $setFailData = ['*カナ', 'abあい', '0011ええおお', '漢字も!'];
        $setSuccessData = ['漢字も', 'カタカナも', '今日はグッド！！'];

        $field = 'zenkakuOnly';

        $this->_failSuccessTest($field, $setFailData, $setSuccessData);
    }

    /**
     * telFaxJp テスト
     *
     * @return void
     */
    public function testValidataionTelFaxJp(): void
    {
        $setFailData = ['03-111111-22222', 'aaa-cc-111', 'あああ-222' ];
        $setSuccessData = ['03-1111-2222', '0565-23-2222', '011-222-1111'];

        $field = 'telFaxJp';

        $this->_failSuccessTest($field, $setFailData, $setSuccessData);
    }

    /**
     * mobileEmailJp テスト
     *
     * @return void
     */
    public function testValidataionMobileEmailJp(): void
    {
        $setFailData = ['hoge', 'aa@aaaa', 'aa#!"@aa.com' ];
        $setSuccessData = ['hoge@docomo.ne.jp', 'hoge..aa@ezweb.ne.jp', 'a_._.e@softbank.ne.jp'];

        $field = 'mobileEmailJp';

        $this->_failSuccessTest($field, $setFailData, $setSuccessData);
    }

    /**
     * passwordValid テスト
     *
     * @return void
     */
    public function testValidataionPasswordValid(): void
    {
        $setFailData = ['hoge', 'aa@aaaa', 'aa#!"@aa.com','あああああ','123456789aa' ];
        $setSuccessData = ['hogeaaaa', '12345567', 'aaa13', '123456789a'];

        $field = 'passwordValid';
        $field_conf = 'password_conf';

        // 失敗パターン
        $data = [];
        foreach ($setFailData as $value) {
            $data['ValidationRule'][$field] = $value;
            $data['ValidationRule'][$field_conf] = $value;
            $this->assertIdentical($this->ValidationRule->create($data), $data);
            $this->assertFalse($this->ValidationRule->validates());
            $this->assertTrue(array_key_exists($field, $this->ValidationRule->validationErrors));
        }

        // 成功パターン
        $data = [];
        foreach ($setSuccessData as $value) {
            $data['ValidationRule'][$field] = $value;
            $data['ValidationRule'][$field_conf] = $value;
            $this->assertIdentical($this->ValidationRule->create($data), $data);
            $this->assertTrue($this->ValidationRule->validates());
            $this->assertFalse(array_key_exists($field, $this->ValidationRule->validationErrors));
        }
    }

    /**
     * 比較対象のフィールドが存在しない場合でもエラーが出ないか確認テスト
     *
     * @return void
     */
    public function testValidataionCompare2fieldWithEmptyField(): void
    {
        $data = [
            'ValidationRule' => [
                'valuediff' => 'あいうえお',
            ],
        ];

        $this->assertIdentical($this->ValidationRule->create($data), $data);
        $this->assertFalse($this->ValidationRule->validates());

        $this->assertTrue(array_key_exists('valuediff', $this->ValidationRule->validationErrors));
    }

    /**
     * testDatetimeYyyymmdd method
     *
     * @return void
     */
    public function testDatetimeYyyymmdd(): void
    {
        $setSuccessData = ['2006-12-27 12:22', '2006.12.27 12:22AM', '2006/12/27 12:22PM', '2006 12 27 12:22' ];
        $setFailData = ['2006-11-31 12:22', '2006.11.31 12:22', '2006/11/31 12:22', '2006 11 31 12:22', ''];

        $field = 'datetime_valid';

        $this->_failSuccessTest($field, $setFailData, $setSuccessData);
    }

    public function testDateTimeField(): void
    {
        $data = [
            'ValidationRule' => [
                'datetime_valid' => [
                    'year' => '2013',
                    'month' => '01',
                    'day' => '24',
                ],
            ],
        ];
        $this->ValidationRule->create($data);
        $this->assertTrue($this->ValidationRule->validates());
        $this->assertEquals('2013-01-24 00:00:00', $this->ValidationRule->data['ValidationRule']['datetime_valid']);
    }
}
