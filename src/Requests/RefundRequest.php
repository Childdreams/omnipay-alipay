<?php

namespace Omnipay\Alipay\Requests;

use Omnipay\Alipay\Responses\RefundResponse;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;

class RefundRequest extends Request
{

    protected $service = 'refund_fastpay_by_platform_pwd';


    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->setDefaults();

        $this->validate(
            'partner',
            '_input_charset',
            'refund_date',
            'batch_no',
            'refund_items'
        );

        $this->validateOne(
            'seller_user_id',
            'seller_email'
        );

        $this->setBatchNum(count($this->getRefundItems()));
        $this->setRefundDetail($this->getDetailData());

        $data = array (
            'service'        => $this->service,
            'partner'        => $this->getPartner(),
            'notify_url'     => $this->getNotifyUrl(),
            'seller_user_id' => $this->getPartner(),
            'refund_date'    => $this->getRefundDate(),
            'batch_no'       => $this->getBatchNo(),
            'batch_num'      => $this->getBatchNum(),
            'detail_data'    => $this->getDetailData(),
            '_input_charset' => $this->getInputCharset()
        );

        $data['sign']      = $this->sign($data, $this->getSignType());
        $data['sign_type'] = $this->getSignType();

        return $data;
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     *
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->response = new RefundResponse($this, $data);
    }


    /**
     * @return mixed
     */
    public function getPartner()
    {
        return $this->getParameter('partner');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setPartner($value)
    {
        return $this->setParameter('partner', $value);
    }


    /**
     * @return mixed
     */
    public function getInputCharset()
    {
        return $this->getParameter('_input_charset');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setInputCharset($value)
    {
        return $this->setParameter('_input_charset', $value);
    }


    /**
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return $this->getParameter('notify_url');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setNotifyUrl($value)
    {
        return $this->setParameter('notify_url', $value);
    }


    /**
     * @return mixed
     */
    public function getSellerEmail()
    {
        return $this->getParameter('seller_email');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setSellerEmail($value)
    {
        return $this->setParameter('seller_email', $value);
    }


    /**
     * @return mixed
     */
    public function getSellerUserId()
    {
        return $this->getParameter('seller_user_id');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setSellerUserId($value)
    {
        return $this->setParameter('seller_user_id', $value);
    }


    /**
     * @return mixed
     */
    public function getSellerId()
    {
        return $this->getSellerUserId();
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setSellerId($value)
    {
        return $this->setSellerUserId($value);
    }


    /**
     * @return mixed
     */
    public function getRefundDate()
    {
        return $this->getParameter('refund_date');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setRefundDate($value)
    {
        return $this->setParameter('refund_date', $value);
    }


    /**
     * @return mixed
     */
    public function getBatchNo()
    {
        return $this->getParameter('batch_no');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setBatchNo($value)
    {
        return $this->setParameter('batch_no', $value);
    }


    protected function setDefaults()
    {
        if (! $this->getRefundDate()) {
            $this->setRefundDate(date('Y-m-d H:i:s'));
        }

        if (! $this->getBatchNo()) {
            $this->setBatchNo(date('Ymd') . mt_rand(1000, 9999));
        }
    }


    /**
     * @return mixed
     */
    public function getRefundItems()
    {
        return $this->getParameter('refund_items');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setRefundItems($value)
    {
        return $this->setParameter('refund_items', $value);
    }


    protected function getDetailData()
    {
        $strings = array ();

        foreach ($this->getRefundItems() as $item) {
            $item = (array) $item;

            if (! isset($item['out_trade_no'])) {
                throw new InvalidRequestException('The field `out_trade_no` is not exist in item');
            }

            if (! isset($item['amount'])) {
                throw new InvalidRequestException('The field `amount` is not exist in item');
            }

            if (! isset($item['reason'])) {
                throw new InvalidRequestException('The field `reason` is not exist in item');
            }

            $strings[] = implode('^', array ($item['out_trade_no'], $item['amount'], $item['reason']));
        }

        return implode('#', $strings);
    }


    /**
     * @return mixed
     */
    protected function getRefundDetail()
    {
        return $this->getParameter('refund_detail');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    protected function setRefundDetail($value)
    {
        return $this->setParameter('refund_detail', $value);
    }


    /**
     * @return mixed
     */
    public function getBatchNum()
    {
        return $this->getParameter('batch_num');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setBatchNum($value)
    {
        return $this->setParameter('batch_num', $value);
    }
}