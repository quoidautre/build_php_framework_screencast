<?php
/**
 *
 * Request handling logic - it's all about creating the appropriate Respose to the certain Request.
 *
 * @author      Andrey I. Esaulov <aesaulov@me.com>
 * @package     build_php_framework_screencast
 * @version     0.2
 */
namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class Framework
{
    protected $matcher;
    protected $resolver;

    // In order to use test doubles in phpunit, we need to replace the concrete classes with interfaces
    public function __construct(UrlMatcherInterface $matcher, ControllerResolverInterface $resolver)
    {
        $this->matcher  = $matcher;
        $this->resolver = $resolver;
    }

    public function handle(Request $request)
    {
        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->resolver->getController($request);
            $arguments = $this->resolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            return new Response('Nope, no such page!', 404);
        } catch (\Exception $e) {
            return new Response("Something went terribly wrong. Server is confused. What have you done?! We are all doomed! \n" . $e->getMessage() . "\n" .  $e->getTraceAsString(), 500);
        }

    }
}