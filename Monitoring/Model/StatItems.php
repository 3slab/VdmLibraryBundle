<?php

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class StatItems
{    
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $value;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var float
     */
    private $sampleRate;

    /**
     * StatItems constructeur
     * 
     * @param string $method The function to call.
     * @param string $label The metric(s) to increment.
     * @param int $value the amount to increment by (default 1)
     * @param array $tags Key Value array of Tag => Value, or single tag as string
     * @param float $sampleRate the rate (0-1) for sampling.
     */
    public function __construct(string $method, string $label, int $value = 1, array $tags = [], float $sampleRate = 1.0)
    {
        $this->method = $method;
        $this->label = $label;
        $this->value = $value;
        $this->tags = $tags;
        $this->sampleRate = $sampleRate;
    }

    public function __toString()
    {
        return json_encode([
            'method' => $this->method,
            'label' => $this->label,
            'value' => $this->value,
            'tags' => $this->tags,
            'sampleRate' => $this->sampleRate,
        ]);
    }


    /**
     * Get the value of method
     *
     * @return  string
     */ 
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set the value of method
     *
     * @param  string  $method
     *
     * @return  self
     */ 
    public function setMethod(string $method): StatItems
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get the value of label
     *
     * @return  string
     */ 
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param  string  $label
     *
     * @return  self
     */ 
    public function setLabel(string $label): StatItems
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of value
     *
     * @return  int
     */ 
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @param  int  $value
     *
     * @return  self
     */ 
    public function setValue(int $value): StatItems
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of tags
     *
     * @return  array
     */ 
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set the value of tags
     *
     * @param  array  $tags
     *
     * @return  self
     */ 
    public function setTags(array $tags): StatItems
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the value of sampleRate
     *
     * @return  float
     */ 
    public function getSampleRate(): float
    {
        return $this->sampleRate;
    }

    /**
     * Set the value of sampleRate
     *
     * @param  float  $sampleRate
     *
     * @return  self
     */ 
    public function setSampleRate(float $sampleRate): StatItems
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }
}
