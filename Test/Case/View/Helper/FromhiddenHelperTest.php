<?php

App::import('Controller', 'Controller');
App::import('Helper', 'Html');
App::import('Helper', 'Form');
App::import('Helper', 'Cakeplus.Formhidden');

class ContactTestController extends Controller
{
    /**
     * name property
     *
     * @var string 'ContactTest'
     */
    public $name = 'ContactTest';

    /**
     * uses property
     *
     * @var mixed null
     */
    public $uses = null;
}

class FormhiddenHelperTest extends CakeTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->Controller = new ContactTestController();
        $this->View = new View($this->Controller);

        $this->Formhidden = new FormhiddenHelper($this->View);
        $this->Formhidden->Form = new FormHelper($this->View);
        $this->Formhidden->Form->Html = new HtmlHelper($this->View);
    }

    public function tearDown(): void
    {
        ClassRegistry::removeObject('view');
        unset($this->Formhidden, $this->Controller, $this->View);
    }

    /**
     * test for using parameter
     *
     * @return void
     */
    public function testBasicHiddenParamData(): void
    {
        $data = [
            'Contact' => [
                'id' => '1',
                'text' => 'aaaa',
                'body' => 'あいうえおテスト日本語1234abcd',
            ],
        ];

        $expected = [
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'data[Contact][id]',
                    'value' => '1',
                    'id' => 'ContactId',
                ],
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'data[Contact][text]',
                    'value' => 'aaaa',
                    'id' => 'ContactText',
                ],
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'data[Contact][body]',
                    'value' => 'あいうえおテスト日本語1234abcd',
                    'id' => 'ContactBody',
                ],
            ],
        ];

        // check not using.
        $this->Formhidden->data = ['Hoge' => ['id' => '199', 'hoge' => 'eeeee']];

        // for using Form->hidden() method which uses $this->data to create hidden tag.
        $this->Formhidden->Form->data = $data;

        $result = $this->Formhidden->hiddenVars($data);

        $this->assertTags($result, $expected);
    }

    /**
     * test for using $this->data
     *
     * @return void
     */
    public function testBasicHiddenThisData(): void
    {
        $data = [
            'Contact' => [
                'id' => '1',
                'text' => 'aaaa',
                'body' => 'あいうえおテスト日本語1234abcd',
            ],
        ];

        $expected = [
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'data[Contact][id]',
                    'value' => '1',
                    'id' => 'ContactId',
                ],
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'data[Contact][text]',
                    'value' => 'aaaa',
                    'id' => 'ContactText',
                ],
            ],
            [
                'input' => [
                    'type' => 'hidden',
                    'name' => 'data[Contact][body]',
                    'value' => 'あいうえおテスト日本語1234abcd',
                    'id' => 'ContactBody',
                ],
            ],
        ];

        $this->Formhidden->data = $data;
        $this->Formhidden->Form->data = $data;
        $result = $this->Formhidden->hiddenVars();
        $this->assertTags($result, $expected);
    }

    /**
     * test for no data
     *
     * @return void
     */
    public function testBasicHiddenNull(): void
    {
        $data = [];

        $this->Formhidden->data = $data;
        $this->Formhidden->Form->data = $data;

        $result = $this->Formhidden->hiddenVars();
        $this->assertNull($result);

        $result = $this->Formhidden->hiddenVars($data);
        $this->assertNull($result);
    }
}
