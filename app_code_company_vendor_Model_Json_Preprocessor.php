<?php #this is not the complete file, just a snippet, hardcoded for fr_FR
    # ...
        public function process(Chain $chain)
        {
            # ...
            $locale = $context->getLocale();
            $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);
            
            #0 récupérer le dictionnaire INCOMPLET (alors qu'il devrait être complet)
            $dict = $this->dataProvider->getData($themePath);
            
            if($locale == 'fr_FR')
            {
                #1 ajouter manuellement des traductions
                $array_manual_translate = array(
                    #"Minimum length of this field must be equal or greater than %1 symbols. Leading and trailing spaces will be ignored." => "Au moins %1 caractères, espaces ignorés au début et à la fin.",
                    // "Minimum of different classes of characters in password is %1. Classes of characters: Lower Case, Upper Case, Digits, Special Characters." => "Au moins %1 caractères dans votre mot de passe. Incluant minuscule, capitale, chiffre, caractères spéciaux.",
                    // "Minimum length of this field must be equal or greater than 8 symbols. Leading and trailing spaces will be ignored." => "Au moins 8 caractères, espaces ignorés au début et à la fin.",
                );
                
                
                #2 aller chercher le fichier de traductions
                #$read = $this->fileReadFactory->create($filePath[0], \Magento\Framework\Filesystem\DriverPool::FILE);
                #$content = $read->readAll();
                $filePath = BP . '/app/i18n/imaginaerum/fr_fr/dictionary.csv';
                $logger->info('preprocessor OVERRIDEN: open '.$filePath);
                if (file_exists($filePath)) {
                
                    // $fileCsv = new \Magento\Framework\File\Csv();
                    // $array_manual_translate = $fileCsv->getData($filePath);
                    
                    
                    $handle = fopen($filePath, "r");
                    $logger->info('preprocessor OVERRIDEN: open '.$filePath);
                    if ($handle) {
                        $logger->info('preprocessor OVERRIDEN: processing dictionnary... ');
                    
                        while (($line = fgets($handle)) !== false) {
                        
                            $debug = False;
                            if(strpos($line, 'Minimum of different') !== False)
                            {
                                #$debug = True;
                            }
                            if($debug){
                                $logger->info( print_r($line, True) );
                            }
                        
                            $phrase = "";
                            $translatedPhrase = "";
                            if(strpos($line, '","') !== False)
                            {
                                $trad_elements = explode('","', $line);
                                $phrase = substr( $trad_elements[0], 1); #remove also the first " at the beginning
                                $translatedPhrase = $trad_elements[1];
                                #$translatedPhrase = substr_replace( $translatedPhrase, "", -1); #remove last end of line
                                $translatedPhrase = rtrim( $translatedPhrase, "\n"); #remove last end of line
                                $translatedPhrase = rtrim( $translatedPhrase, '"');  #remove also the last " at the end
                                
                                if($debug){
                                    $logger->info('avec guillemets');
                                }
                                
                            }
                            else{ #si aps de guillement (simple mot)
                                $trad_elements = explode(',', $line);
                                $phrase = $trad_elements[0];
                                $translatedPhrase = $trad_elements[1];
                                $translatedPhrase = rtrim( $translatedPhrase, "\n"); #remove last end of line
                                
                                if($debug){
                                    $logger->info('sans guillemets');
                                }
                            }
                            
                            #ligne qui fait le truc (et erreur)
                            #$logger->info(print_r($trad_elements, true));
                            $array_manual_translate[$phrase] = $translatedPhrase;
                            
                            if($debug)
                            {
                                $logger->info(print_r($phrase, true));
                                $logger->info(print_r($translatedPhrase, true));
                            }
                        }
                        
                        fclose($handle);
                        $logger->info('preprocessor OVERRIDEN: fermeture fichier source ');
                    
                    } else {
                        $logger->info('preprocessor OVERRIDEN: error opening  '.$filePath);
                    } 
                    
                    
                    #3 parcourir et peupler mon dictionnaire perso
                    $logger->info('preprocessor OVERRIDEN: debut remplissage dico ');
                    foreach($array_manual_translate as $phrase => $translatedPhrase)
                    {
                        
                        $debug = False;
                        if(strpos($phrase, 'Minimum of different') !== False)
                        {
                            #$debug = True;
                        }
                        if($debug) {
                            $logger->info($phrase);
                            $logger->info($translatedPhrase);
                        }
                    
                    
                        $dict[$phrase] = $translatedPhrase;
                    }
                    $logger->info('preprocessor OVERRIDEN: fin remplissage dico ');
                }
            }
            //fin fix pour traductions
            $logger->info('preprocessor OVERRIDEN: debut encodage ');
            $chain->setContent(json_encode($dict));
            $chain->setContentType('json');
