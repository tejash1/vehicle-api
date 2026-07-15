<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ConflictException;
use App\Exception\ResourceNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $appEnv,
    ) {}

    public static function getSubscribedEvents(): array
    {
        // Priority 10 runs before Symfony's built-in exception listener (priority 0)
        return [KernelEvents::EXCEPTION => ['onKernelException', 10]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        // Only intercept /api routes — let non-API routes use Symfony's default handler
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $throwable = $event->getThrowable();
        [$status, $message, $extra] = $this->resolve($throwable);

        $body = ['error' => Response::$statusTexts[$status] ?? 'Error', 'message' => $message, 'code' => $status];

        if ($extra !== []) {
            $body['violations'] = $extra;
        }

        $event->setResponse(new JsonResponse($body, $status));
    }

    /**
     * @return array{int, string, array<string, string[]>}
     */
    private function resolve(\Throwable $t): array
    {
        // Validation failure from #[MapRequestPayload] / #[MapQueryString]
        if ($t instanceof UnprocessableEntityHttpException
            && $t->getPrevious() instanceof ValidationFailedException
        ) {
            return [422, 'Validation failed.', $this->formatViolations($t->getPrevious())];
        }

        return match (true) {
            $t instanceof ResourceNotFoundException => [404, $t->getMessage(), []],
            $t instanceof ConflictException         => [409, $t->getMessage(), []],
            $t instanceof HttpExceptionInterface    => [$t->getStatusCode(), $t->getMessage() ?: Response::$statusTexts[$t->getStatusCode()], []],
            default                                 => [500, $this->appEnv === 'prod' ? 'An internal server error occurred.' : $t->getMessage(), []],
        };
    }

    /** @return array<string, string[]> */
    private function formatViolations(ValidationFailedException $e): array
    {
        $result = [];

        foreach ($e->getViolations() as $violation) {
            $result[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $result;
    }
}
