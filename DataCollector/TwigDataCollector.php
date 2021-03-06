<?php

/*
 *
 * (c) Vincent Bouzeran <vincent.bouzeran@elao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elao\WebProfilerExtraBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AsseticDataCollector.
 *
 * @author Vincent Bouzeran <vincent.bouzeran@elao.com>
 */
class TwigDataCollector extends DataCollector
{
    private $twig;

    /**
     * The Constructor for the Twig Datacollector
     *
     * @param \Twig_Environment $twig Twig Enviroment
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Collect information from Twig
     *
     * @param Request    $request   The Request Object
     * @param Response   $response  The Response Object
     * @param \Exception $exception The Exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $filters = array();
        $tests = array();
        $extensions = array();
        $functions = array();

        foreach ($this->twig->getExtensions() as $extensionName => $extension) {
            $extensions[] = array(
                'name' => $extensionName,
                'class' => get_class($extension)
            );
            foreach ($extension->getFilters() as $filterName => $filter) {
                if ($filter instanceof \Twig_FilterInterface) {
                    $call = $filter->compile();
                } else {
                    $call = $filter->getName();
                }

                $filters[] = array(
                    'name' => $filterName,
                    'extension' => $extensionName,
                    'call' => $call,
                );
            }

            foreach ($extension->getTests() as $testName => $test) {
                if ($test instanceof \Twig_TestInterface) {
                    $call = $test->compile();
                } else {
                    $call = $test->getName();
                }

                $tests[] = array(
                    'name' => $testName,
                    'extension' => $extensionName,
                    'call' => $call,
                );
            }

            foreach ($extension->getFunctions() as $functionName => $function) {
                if ($function instanceof \Twig_FunctionInterface) {
                    $call = $function->compile();
                } else {
                    $call = $function->getName();
                }

                $functions[] = array(
                    'name' => $functionName,
                    'extension' => $extensionName,
                    'call' => $call,
                );
            }
        }

        $this->data['extensions'] = $extensions;
        $this->data['tests'] = $tests;
        $this->data['filters'] = $filters;
        $this->data['functions'] = $functions;
    }

    /**
     * Collects data on the twig templates rendered
     *
     * @param mixed $name       The template name
     * @param array $parameters The array of parameters passed to the template
     */
    public function collectTemplateData($name, $parameters)
    {
        $collectedParameters = array();
        foreach ($parameters as $name => $value) {
            $collectedParameters[$name] = in_array(gettype($value), array('object', 'resource'))
                ? get_class($value)
                : gettype($value);
        }

        $this->data['templates'][] = array(
            'name' => $name,
            'parameters' => $collectedParameters
        );
    }

    /**
     * Returns the amount of Templates
     *
     * @return int Amount of templates
     */
    public function getCountTemplates()
    {
        return count($this->getTemplates());
    }

    /**
     * Returns the Twig templates information
     *
     * @return array Template information
     */
    public function getTemplates()
    {
        return $this->data['templates'];
    }

    /**
     * Returns the amount of Extensions
     *
     * @return integer Amount of Extensions
     */
    public function getCountExtensions()
    {
        return count($this->getExtensions());
    }

    /**
     * Returns the Twig Extensions Information
     *
     * @return array Extension Information
     */
    public function getExtensions()
    {
        return $this->data['extensions'];
    }

    /**
     * Returns the amount of Filters
     *
     * @return integer Amount of Filters
     */
    public function getCountFilters()
    {
        return count($this->getFilters());
    }

    /**
     * Returns the Filter Information
     *
     * @return array Filter Information
     */
    public function getFilters()
    {
        return $this->data['filters'];
    }

    /**
     * Returns the amount of Twig Tests
     *
     * @return integer Amount of Tests
     */
    public function getCountTests()
    {
        return count($this->getTests());
    }

    /**
     * Returns the Tests Information
     *
     * @return array Tests Information
     */
    public function getTests()
    {
        return $this->data['tests'];
    }

    /**
     * Returns the amount of Twig Functions
     *
     * @return integer Amount of Functions
     */
    public function getCountFunctions()
    {
        return count($this->getFunctions());
    }

    /**
     * Returns Twig Functions Information
     *
     * @return array Function Information
     */
    public function getFunctions()
    {
        return $this->data['functions'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig';
    }
}
