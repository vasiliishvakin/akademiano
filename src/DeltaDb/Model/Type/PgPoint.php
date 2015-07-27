<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Type;


use DeltaCore\Prototype\ArrayableInterface;

class PgPoint implements \JsonSerializable, ArrayableInterface
{
    protected $lat;
    protected $lon;

    function __construct(array $array = null)
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

    public function format($format = "%1s %2s")
    {
        return sprintf($format, $this->getLon(), $this->getLat());
    }

    function jsonSerialize()
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

} 