<?php

/**
 * Class SoapTransport
 *
 * @method GeocodeAddressNonParsed(array $aParams)
 */
class SoapTransport
{
    private $localClient = null;
    private $wsdl = "https://geoservices.tamu.edu/Services/Geocode/WebService/GeocoderService_V04_01.asmx?WSDL";

    public function getClient() {
        if ($this->localClient == null) {
            $this->localClient = new \SoapClient($this->wsdl, ['soap_version' => SOAP_1_2]);
        }
        return $this->localClient;
    }

    public function __call($name, $params) {
        return $this->getClient()->__soapCall($name, $params);
    }
}