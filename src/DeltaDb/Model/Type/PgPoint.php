<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Type;


use DeltaDb\Model\Type\ReservableInterface;
use DeltaUtils\Object\Prototype\StringableInterface;
use DeltaUtils\StringUtils;
use DeltaUtils\Object\Prototype\ArrayableInterface;

class PgPoint implements \JsonSerializable, ArrayableInterface, StringableInterface, ReservableInterface
{
    protected $lat;
    protected $lon;

    public function __construct(array $array = null)
    {
        if ($array) {
            $this->setLat($array['lat']);
            $this->setLon($array['lon']);
        }
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lon
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
    }

    /**
     * @return mixed
     */
    public function getLon()
    {
        return $this->lon;
    }

    public function __toString()
    {
        return $this->format();
    }

    public function format($format = '%1$s %2$s')
    {
        return sprintf($format, $this->getLon(), $this->getLat());
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'lon' => $this->getLon(),
            'lat' => $this->getLat(),
        ];
    }

    public function toReserve($adapter = null)
    {
        if (null === $adapter) {
            $adapter = "PgsqlAdapter";
        }
        $adapter = StringUtils::cutClassName($adapter);
        switch ($adapter) {
            case "PgsqlAdapter" :
                return $this->format('ST_GeographyFromText(\'SRID=4326; POINT(%1$s %2$s)\')');
            default:
                throw  new \LogicException("Method not implement for adapter {$adapter}");
        }
    }
}
