<?php
namespace Copiaincolla\CodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/*
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdEntity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdEntity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
*/
use Symfony\Component\Finder\Finder;


use \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GenerateVhostsCommand extends ContainerAwareCommand
{
    /**
     * directory assoluta dei progetti (siti web) su sangria
     */
    private $projectsRoot = '/var/www/vhosts';

    /**
     * cartella di destinazione
     */
    private $destinationFolder = '/etc/apache2/sites-enabled/sangria.vhosts';
    private $destinationSubfolder = 'sangria.vhosts/';
    private $backupsFolder;


    /**
     * regex di nomi di file (importanti) per cui creare un virtual host
     *
     * es: frontend_dev.php
     *     index.php
     *     ...
     *
     * se viene trovato questo file, viene creato un virtual host
     *
     * @var unknown_type
     */
    private $matchingControllerFileNamesRegex = '#^index.php$|^frontend.php$|^backend.php$|^frontend_dev.php$|^backend_dev.php$|^app.php$|^app_dev.php$|^index.html$|^index.htm$#';

    /**
     * array delle cartelle che possono contenere i file importanti per cui creare i virtual host
     *
     * index.php, app.php, etc.. possono essere direttamente dentro solo a queste cartelle
     *
     * @var unknown_type
     */
    private $matchingControllerFileNamesParentFolders = array(
        'web',
        'www',
        'current',
        'jury',
        'emanuele',
        'andrea',
        'tazio',
        'diego',
        'sebastiano',
        'git'
    );

    /**
     * array delle cartelle di cui creare il sottodominio di terzo livello
     *
     * genera anche un virtual host per la root del progetto, se c'è un file importante dentro
     *
     * @var unknown_type
     */
    private $matchingSubdomainFolders = array(
        'andrea',
        'emanuele',
        'jury',
        'mattia',
        'tazio',
        'current',
        'git'
    );

    // InputInterface e OutputInterface salvate come variabili di classe
    private $input;
    private $output;

    /**
     * configurazione del comando
     */
    protected function configure()
    {
        $this
            ->setName('damigiana:generate-vhosts')
            ->setDescription('Genera i Virtual hosts su sangria')
            // necessario per eseguire il comando
            ->addOption('force', null, InputOption::VALUE_NONE, 'specifica questa opzione per eseguire il comando')

            // scrive i file in app/cache invece che nella cartella vera
            ->addOption('write-in-cache', null, InputOption::VALUE_NONE, "scrivi i file nella cartella app/cache")

            // effettua il backup nella cartella di destinazione
            ->addOption('no-backup', null, InputOption::VALUE_NONE, "NON effettuare il backup")

            // stampa una richiesta di conferma epr proseguire
            ->addOption('by-steps', null, InputOption::VALUE_NONE, "chiede conferma ad ogni passaggio prima di processare il progetto seguente")
        ;
    }

    /**
     * esegue il comando
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // setta $input e $output
        $this->input = $input;
        $this->output = $output;

        /**
         * check --force option
         */
        if (!$input->getOption('force')) {
            $output->writeln('<error> è necessario specificare l\'opzione --force per eseguire il comando</error>');
            $output->writeln('');
            return;
        }

        /**
         * calcola la cartella di destinazione dei files
         */
        if (!$this->configureDestinationFolder()) {
            return;
        }

        /**
         * stampa un dialogo di conferma
         */
        $output->writeln('');
        $output->writeln('<info>Procedura di creazione dei file contenenti i virtual hosts</info>');
        $output->writeln(" ^cartella di destinazione: {$this->destinationFolder}");
        $output->writeln('');

        if (!$this->getHelperSet()->get('dialog')->askConfirmation($output, '<question>Vuoi proseguire? (y/N)</question>', false)) {
            return;
        }

        /**
         * esegue un backup in un file .tgz nella cartella di destinazione
         */
        if ($input->getOption('no-backup') != true) {

            // setta la cartella per i backups
            $this->backupsFolder = $this->getContainer()->getParameter('kernel.root_dir').'/../vhosts-backups/';

            // effettua il backup
            $this->doBackup();
        }

        /**
         * inizio procedura di creazione dei virtual hosts
         */
        $finderProjects = new Finder();

        /**
         * itera sulle cartelle (figlie dirette) che stanno nella cartella radice di tutti i progetti
         */
        foreach (
            $finderProjects
                ->directories()
                ->in($this->projectsRoot)
                ->sortByName()
                ->depth('== 0')
        	    ->ignoreDotFiles(true)
        	    ->ignoreVCS(true)
            as $dir
        ) {
            // nome del progetto (nome della cartella che lo contiene)
            $projectName = $dir->getFilename();

            // stringa inizio di ogni interazione
    	    $output->writeln('<info>=================================================================================================================================</info>');
    	    $output->writeln("<info>Directory progetto: {$dir->getRealPath()}</info>");
    	    $output->writeln('<info>=================================================================================================================================</info>');

    	    // inizializza l'array dei virtual host da scrivere nel file
    	    $arrayVirtualHosts = array();

    	    /**
    	     * aggiunge subito il virtual host relativo alla root del progetto
    	     */
    	    // document root for apache
    	    $virtualHostUrl = $this->normalizza($projectName).'.ufficio';
    	    $arrayVirtualHosts = $this->addVirtualHost($arrayVirtualHosts, $virtualHostUrl, $dir->getRealPath());

    	    /**
    	     * cerca un file importante (es: frontend_dev.php) all'interno della cartella del progetto
    	     */
    	    $finderFiles = new Finder();
    	    $matchingFiles = $finderFiles
        	    ->files()
        	    ->ignoreDotFiles(true)
        	    ->ignoreVCS(true)

        	    // cerca all'interno della directory del progetto
        	    ->in($dir->getRealpath())

        	    // il nome del file importante deve coincidere con deller egole regez definite in $this->matchingControllerFileNamesRegex
        	    ->name($this->matchingControllerFileNamesRegex)

        	    // esclude alcune directory figlie
        	    ->exclude('backup')
        	    ->exclude('current_old')
        	    ->exclude('old')
        	    ->exclude('vendor')
        	    ->exclude('lib')
        	    ->exclude('plugin')
        	    ->exclude('data')
        	    ->exclude('web/assets')

        	    ->exclude('.rsync_cache')
    	    ;

    	    /**
    	     * itera sui file importanti che trova riempiendo l'array dei virtual host per una certa cartella
    	     */
    	    foreach ($matchingFiles as $file) {

    	        // path del file importante trovato
    	        $fileRealPath = $file->getRealpath();

    	        // output
        	    $output->writeln("  File importante trovato: <comment>{$fileRealPath}</comment>");

    	        // document root for apache
    	        $documentRoot = substr($fileRealPath, 0, strrpos($fileRealPath, '/'));

    	        // escape degli spazi per il document root

    	        /**
    	         * esplode la stringa del percorsod el file in un array
    	         */
    	        $urlToTokens = explode('/', $fileRealPath);

    	        /**
    	         * key nell'array $urlToTokens della cartella root del progetto
    	         */
    	        $urlToTokensProjectNameIndex = array_search($projectName, $urlToTokens);

    	        if (!$urlToTokensProjectNameIndex) continue;

    	        /**
    	         * cartella figlia diretta della root del progetto in cui è stato trovato il file
    	         *
    	         * es: per /var/www/vhosts/SonnLeonardo/Presentazione/index.html è "Presentazione"
    	         */
    	        $directChildFolder = $urlToTokens[$urlToTokensProjectNameIndex+1];


    	        /**
    	         * cartella contenente il file importante (es: web)
    	         */
    	        $fileParentFolder = $urlToTokens[count($urlToTokens) - 2];

    	        //var_dump($fileParentFolder, $directChildFolder);
    	        if (
	                // la cartella che contiene il file importante fa parte dell'array di quelle accettabili
	                in_array($fileParentFolder, $this->matchingControllerFileNamesParentFolders)
    	                ||
	                // il file importante è nella root del progetto
	                $fileParentFolder == $projectName
                )
    	        {
	                // l'url completo del virtual host
	                $virtualHostUrl = $this->normalizza($projectName).'.ufficio';

	                /**
	                 * aggiunge il terzo livello se necesario
	                 */
	                if (
	                        // la cartella fa parte dell'array di quelle accettabili
	                        in_array($directChildFolder, $this->matchingSubdomainFolders)
	                        &&
	                        // il file importante non è nella root del progetto
	                        $fileParentFolder != $projectName
	                )
	                {
    	                if ($directChildFolder != $file->getFileName() && $directChildFolder != $projectName ) {
    	                    $virtualHostUrl = $directChildFolder.'.'.$virtualHostUrl;
    	                }
	                }


	                // aggiunge il virtual host all'array dei virtual host
	                $arrayVirtualHosts = $this->addVirtualHost($arrayVirtualHosts, $virtualHostUrl, $documentRoot);

	                continue;
    	        } else {
    	            $output->writeln('  Il file trovato non è dentro la root del progetto o in una cartella ammissibile');
    	            $output->writeln('');
    	        }

    	    }

    	    /**
    	     * scrive il file dei virtual host
    	     */
    	    // calcola il filename normalizzandolo (es: "-" al posto degli spazi)
    	    $filename = $this->normalizza($projectName);
    	    // scrive i virtual hosts
    	    $this->writeToVirtualHostFile($arrayVirtualHosts, $filename.'.ufficio', true);

    	    /**
    	     * riepilogo
    	     */
    	    $this->output->writeln("  <info>Riepilogo degli host scritti nel file:</info> <comment>$filename</comment>");
    	    foreach ($arrayVirtualHosts as $k => $v) {
    	        $this->output->writeln("    {$k} => {$v['document_root']}");
    	    }
    	    $this->output->writeln('');


            // righe vuote
    	    $output->writeln('');

    	    if ($input->getOption('by-steps')) {
    	        if (!$this->getHelperSet()->get('dialog')->askConfirmation($output, '<question>  Vuoi proseguire? (Y/n)</question>', true)) {return;}
    	        $output->writeln('');
    	    }
        }

    }

    /**
     * aggiunge un virtual host alla lista di quelli che verranno scritti nel file
     *
     * @param unknown_type $virtualHostUrl
     * @param unknown_type $documentRoot
     */
    private function addVirtualHost($arrayVirtualHosts, $virtualHostUrl, $documentRoot)
    {
        $arrayVirtualHosts[$virtualHostUrl] = array(
            'document_root'        => $documentRoot,
            'apache_virtualhost'   => $this->getVirtualHostApacheTag($virtualHostUrl, $documentRoot)
        );

        $this->output->write("  <info>Aggiungo il virtual host:</info> <comment>http://{$virtualHostUrl} -> {$documentRoot}</comment>");
        $this->output->writeln('');
        $this->output->writeln('');

        return $arrayVirtualHosts;
    }

    /**
     * calcola la cartella di destinazione dei files contenenti i virtual hosts
     */
    private function configureDestinationFolder()
    {
        /**
         * $this->destinationFolder è già settata di default
         * configura la cartella per scrivere in cache se necessario
         */
        if ($this->input->getOption('write-in-cache')) {
            $this->destinationFolder = $this->getContainer()->getParameter('kernel.root_dir').'/cache/sites-enabled/'.$this->destinationSubfolder;
        }

        /**
         * crea la cartella se necessario
         */
        if (!is_dir($this->destinationFolder)) {
            // dialog di conferma creazione cartella
            if (!$this->getHelperSet()->get('dialog')->askConfirmation($this->output, "<question>È necessario creare la cartella \"{$this->destinationFolder}\". Vuoi proseguire? (y/N)</question>", false)) {
                return false;
            }

            // crea la cartella se nececcario
            try {
                mkdir($this->destinationFolder, 0777, true);
            } catch (\Exception $e) {
                $this->output->writeln("<error>non è possibile creare la cartella di destinazione: {$this->destinationFolder}</error>");
                return false;
            }
        }

        /**
         * imposta i permessi 775 alla cartella di destinazione ed al suo contenuto
         */
        $shellScript = "cd {$this->destinationFolder} && chmod -R 775 .";
        shell_exec($shellScript);

        /**
         * controlla che la cartella di destinazione sia scrivibile
         */
        if (!is_writable($this->destinationFolder)) {
            $this->output->writeln("<error>non è possibile scrivere nella cartella di destinazione: {$this->destinationFolder}</error>");
            return false;
        }

        return true;
    }

    /**
     * effettua un backup di tutto quello che c'è dentro la cartella $this->destinationFolder
     * in un file compresso il cui nome varia in base al minuto
     *
     * @param unknown_type $input
     * @param unknown_type $output
     */
    private function doBackup()
    {
        $datetime = new \DateTime();

        $backupFileFullpath = $this->backupsFolder.'backup_'.($datetime->format('Ymd_Hi').'.tgz');

        $shellScript = "cd {$this->backupsFolder} && tar -cvzf {$backupFileFullpath} {$this->destinationFolder}*";

        $this->output->writeln('<info>Esecuzione del backup in corso...</info>');
        $this->output->writeln("<info>--> il file di backup è: {$backupFileFullpath}</info>");

        //die($shellScript);
        $this->output->writeln(
            shell_exec($shellScript)
        );

        $this->output->writeln('<info>--> backup terminato</info>');
        $this->output->writeln('');
    }

    /**
     * normalizza una stringa
     *
     *  - carattere "-" al posto degli spazi
     */
    private function normalizza($string)
    {
        return preg_replace('#[^A-Za-z0-9-]+#', '-', $string);
    }

    /**
     * ritorna la stringa di testo che serve ad apache per il virtual host
     */
    private function getVirtualHostApacheTag($virtualHostUrl, $documentRoot)
    {
        $str = <<<EOF
#############################################################################
# $virtualHostUrl
#############################################################################
<VirtualHost *>
    ServerName $virtualHostUrl
    DocumentRoot "$documentRoot"

    <Directory "$documentRoot">
        Options +FollowSymLinks +SymLinksIfOwnerMatch
        AllowOverride All
    </Directory>
</VirtualHost>


EOF;
        return $str;
    }

    /**
     * scrive i virtual host di una cartella nel file
     */
    private function writeToVirtualHostFile($arrayVirtualHosts, $filename = null, $toCache = false)
    {

        $destinationFolder = $this->destinationFolder;

        /**
         * filename assoluto del file da scrivere
         *
         * @TODO: sostituire gli spazi con "_"
         */
        $fileRealPath = $destinationFolder.'/'.$filename;

        /**
         * contenuto del file da scrivere
         * @var unknown_type
         */
        $fileContent = <<<EOF
#############################################################################
# file generato automaticamente
# da uno script della console symfony di Damigiana
#############################################################################
EOF;

        foreach ($arrayVirtualHosts as $k => $v) {
            $fileContent .= $v['apache_virtualhost'];
        }

        /**
         * scrive il file dei virtual host
         */
        $fp = fopen($fileRealPath, 'w+');
        fwrite($fp, $fileContent);
        fclose($fp);

        // imposta i permessi
        chmod($fileRealPath, 0777);
    }

}