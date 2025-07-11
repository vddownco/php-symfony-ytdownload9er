<?php

namespace App\Security;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        $userEmail = $user->getUserIdentifier();

        $user = $this->userRepository->findOneByEmail($userEmail);

        // if user is not enabled - deny access
        if (false === $user->isEnabled()) {
            $message = 'Access denied';

            $this->logger->info($message, [
                'user' => $user->getEmail(),
            ]);

            throw new CustomUserMessageAccountStatusException($message);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
