<?php

namespace App\Command;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-pokemon-comment',
    description: 'Add a short description for your command',
)]
class DeletePokemonCommentCommand extends Command
{
    public function __construct(
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pokemonComment = $this->commentRepository->getQbBy(['content' => 'pokemon']);

        if ($pokemonComment !== null) {
            foreach ($pokemonComment as $comment) {
                $this->entityManager->remove($comment);
            }
            $this->entityManager->flush();

            if (count($pokemonComment) == 0) {
                $output->writeln("Aucun commantaire supprimer");
            } else {
                $output->writeln(count($pokemonComment) . " commantaires supprimer");
            }
            return Command::SUCCESS;
        } else {
            $output->writeln("Erreur");
            return Command::FAILURE;
        }
    }
}
