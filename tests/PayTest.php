<?php
/**
 * Created by PhpStorm.
 * User: Artexoid
 * Date: 28.04.16
 * Time: 22:08
 */

use QFive\Artexoid\SBRF\Pay;
use QFive\Artexoid\SBRF\Config;
use QFive\Artexoid\SBRF\ActionExceptions;
use QFive\Artexoid\SBRF\Reverse;
use QFive\Artexoid\SBRF\Refund;


class PayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Тест создания существующего заказа
     */
    public function testOrderExists()
    {

        $this->assertNotEmpty(Config::gI()->testSuccessURL, 'Необходимо установить параметр testSuccessURL в local.config.json');
        $this->assertNotEmpty(Config::gI()->testPayedOrderId, 'Необходимо установить параметр testPayedOrderId в local.config.json');

        $testOrder = Config::gI()->testPayedOrderId;
        try {
            $info = Pay::orderRegister($testOrder, 1, Config::gI()->testSuccessURL);
        } catch (ActionExceptions $e) {
            $this->assertEquals($this->errors['exists']['errorCode'], $e->getCode());
            return;
        }
        $this->assertTrue(false, 'Заказ не существует');
    }


    /**
     * Тест статуса успешно оплаченного
     */
    public function testSuccessPayed()
    {
        $this->assertNotEmpty(Config::gI()->testPayedOrderId, 'Необходимо установить параметр testPayedOrderId в local.config.json');
        /** @var string $testOrder Id уже оплаченного заказа */
        $testOrder = Config::gI()->testPayedOrderId;
        $this->assertTrue(Pay::isApprovedPay($testOrder));
    }

    /**
     * Тест регистрации заказа
     */
    public function testOrderRegister(){
        $this->assertNotEmpty(Config::gI()->testSuccessURL, 'Необходимо установить параметр testSuccessURL в local.config.json');
        $orderId = rand(0,100). time();
        $info = Pay::orderRegister($orderId,1, Config::gI()->testSuccessURL);
        $this->assertNotEmpty($info['orderId'], 'Не получен OrderId');
        $this->assertNotEmpty($info['formUrl'], 'Не получен formUrl');
    }


    public function testOrderCancel(){
        $this->markTestSkipped('Не работает');
        $this->assertNotEmpty(Config::gI()->testPayedOrderId, 'Необходимо установить параметр testSuccessURL в local.config.json');

        $info = Reverse::reverse(Config::gI()->testPayedOrderId);
        var_dump($info);
    }


    public function testOrderRefund(){
        $this->assertNotEmpty(Config::gI()->testPayedOrderId, 'Необходимо установить параметр testPayedOrderId в local.config.json');

        $info = Refund::refund(Config::gI()->testPayedOrderId);
        var_dump($info);
    }

    protected $errors = [
        'exists' => ['errorCode' => '1', 'errorMessage' => 'Заказ с таким номером уже обработан']
    ];
}
