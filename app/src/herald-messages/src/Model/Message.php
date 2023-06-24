<?php


namespace Akademiano\HeraldMessages\Model;


use Akademiano\Entity\ContentEntity;
use Akademiano\Entity\UserInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\UserEO\Model\User;

/**
 * @method EntityOperator getOperator()
 */
class Message extends ContentEntity implements DelegatingInterface
{
    use DelegatingTrait;


    /** @var  string */
    protected $to;
    /** @var  string */
    protected $from;

    /** @var  string */
    protected $replayTo;

    /** @var  Status */
    protected $status = Status::STATUS_NEW;

    /** @var array */
    protected $params = [];

    /** @var  TransportType */
    protected $transport;

    /**
     * @return User
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param User $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return User
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param UserInterface $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getReplayTo(): string
    {
        return $this->replayTo;
    }

    /**
     * @param string $replayTo
     */
    public function setReplayTo(string $replayTo)
    {
        $this->replayTo = $replayTo;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        if (null !== $this->status && !$this->status instanceof Status) {
            $this->status = new Status((integer)$this->status);
        }
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return TransportType
     */
    public function getTransport(): TransportType
    {
        if (!$this->transport instanceof TransportType) {
            $this->transport = new TransportType((integer)$this->transport);
        }
        return $this->transport;
    }

    /**
     * @param TransportType $transport
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['to'] = $this->getTo();
        $data['from'] = $this->getFrom();
        $data['status'] = $this->getStatus();
        $data['transport'] = $this->getTransport();
        $data['params'] = $this->getParams();
        return $data;
    }

    public function getOwner(): ?UserInterface
    {
        return null;
    }


}
