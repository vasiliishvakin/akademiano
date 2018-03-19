<?php


namespace Akademiano\Messages\Model;


use Akademiano\Entity\ContentEntity;
use Akademiano\Entity\UserInterface;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\UserEO\Model\User;


class Message extends ContentEntity
{

    /** @var  User */
    protected $to;
    /** @var  User */
    protected $from;
    /** @var  Status */
    protected $status;

    /** @var array */
    protected $params = [];

    /** @var  TransportType */
    protected $transport;

    /**
     * @return User
     */
    public function getTo()
    {
        if (null !== $this->to && !$this->to instanceof UserInterface) {
            $this->to = $this->delegate((new GetCommand(User::class))->setId($this->to));
        }
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
    public function getFrom()
    {
        if (null !== $this->from && !$this->from instanceof UserInterface) {
            $this->from = $this->delegate((new GetCommand(User::class))->setId($this->from));
        }
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
     * @return Status
     */
    public function getStatus()
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
    public function getParams()
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
    public function getTransport()
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

    public function getContent()
    {
        if (null === $this->content) {
            $this->content = $this->delegate(new ParseMessageCommand($this));
        }
        return $this->content;
    }
}
