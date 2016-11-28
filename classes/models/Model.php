<?php
namespace nigiri\models;

use nigiri\db\DB;
use nigiri\db\DBException;
use nigiri\db\DbResult;
use nigiri\Site;

/**
 * Classe generica che implementa alcuni meccanismi di base utili
 * per tutte le entità che rappresentano record da una tabella del db
 */
abstract class Model
{
    const MODEL_NO_ATTR = "Non si tratta di un attributo di questo model";
    const JOIN_ONE_TO_ONE = 'OneToOne';
    const JOIN_MANY_TO_ONE = 'ManyToOne';
    const JOIN_ONE_TO_MANY = 'OneToMany';
    const JOIN_MANY_TO_MANY = 'ManyToMany';
    /**
     * L'array per memorizzare i campi caricati dal database
     */
    protected $loaded_attributes = array();
    /**
     * Array per memorizzare gli oggetti model derivati dalle associazioni con il model corrente
     */
    protected $loaded_associations = array();
    /**
     * Imposta la modalità di non salvataggio dei dati modificati sul db. Vengono tenuti solo nell'oggetto senza modificare il DB.
     * Utile nei casi che si voglia usare il salvataggio di campi in gruppo o creare un ogetto temporaneo/fittizio
     */
    private $no_save = false;

    /**
     * Costruttore che definisce solo la procedura generica di caricamento degli attributi passati.
     * Tutto il resto è lasciato alle classi figlie.
     * @param $auto_load : Opzionale. Se vero il costruttore carica automaticamente dal DB tutti gli attributi.
     *                    Se è un array che contiene i nomi degil attributi o dei campi del DB da caricare,
     *                    caricherà solo gli attributi specificati. Se false non carica nulla. Se $more_info non è vuoto
     *                    i campi specificati in more info non verranno caricati dal db. Default false.
     * @param $info : Opzionale. è possibile passare un array o un @see DbResult per precaricare dati nell'oggetto
     *                    senza fare query aggiuntive. In caso di DbResult viene considerato solo il primo record
     *                    I campi del risultato o le chiavi dell'array devono corrispondere a nomi di attributi come
     *                    definiti in @see static::getAttributesMap() o a veri campi sul DB o ad associazioni con il pattern:
     *                    <NomeAssociazione>_<nomeAttributoAssociazione>
     * @param $apply_callback : Opzionale. Indica se sui dati in $info è necessario applicare la callback di lettura (@see self::getAttributesMap() 'after_read'). Default true
     */
    public function __construct($auto_load = false, $info = array(), $apply_callback = true)
    {
        $attributes = static::getAttributesMap();
        $associations = static::getJoinsMap();

        if (!empty($info)) {//In caso che magari all'esterno ho già scaricato i dati dell'utente, diamo la possibilità di settarli senza fare altre query
            if ($info instanceof DbResult) {
                $info = $info->fetch();
            }

            $assoc_data = array();
            foreach ($info as $attr => $value) {
                if (static::isMyAttribute($attr)) {
                    $field = self::normalizeAttribute($attr);
                    if ($apply_callback) {
                        $this->loaded_attributes[$field] = self::normalizeAttribute($attr, 'after_read', $value);
                    } else {
                        $this->loaded_attributes[$field] = $value;
                    }
                } elseif ($real_attr = static::isMyDbField($attr)) {
                    if ($apply_callback) {
                        $this->loaded_attributes[$attr] = self::normalizeAttribute($real_attr, 'after_read', $value);
                    } else {
                        $this->loaded_attributes[$attr] = $value;
                    }
                } else {
                    $low_field = strtolower($attr);
                    foreach ($associations as $k => $v) {
                        if (($v['assoc'] == self::JOIN_ONE_TO_ONE || $v['assoc'] == self::JOIN_MANY_TO_ONE)) {
                            if (strpos($low_field, $k . "_") === 0) {
                                $temp = substr($low_field, strlen($k) + 1);
                                if (strlen($temp) > 0) {
                                    $assoc_data[$k][$temp] = $value;
                                }
                                break;
                            }
                        }
                    }
                }
            }
            foreach ($assoc_data as $assoc_name => $data) {
                $model = $associations[$assoc_name]['model'];

                //If Left or Right Join the associated record may not exist and be all NULL
                if (($associations[$assoc_name]['type'] == 'LEFT' || $associations[$assoc_name]['type'] == 'RIGHT')) {
                    $null = true;
                    foreach ($data as $k => $v) {
                        if ($v !== null) {
                            $null = false;
                            break;
                        }
                    }
                    if ($null) {
                        continue;
                    }
                }

                if (class_exists($model)) {
                    $this->loaded_associations[$assoc_name] = $model::buildFromArray($data, $apply_callback);
                } else {
                    $this->loaded_associations[$assoc_name] = new GenericModel($model, $data);
                }
            }
        }

        $to_load = array();
        $assoc_to_load = array();
        if ($auto_load === true) {
            foreach ($attributes as $attr => $field_data) {
                $field = self::normalizeAttribute($attr);
                if (!isset($this->loaded_attributes[$field])) {
                    //I use field as key to avoid requesting the same field more than once
                    //because often there are multiple attributes pointing to the same field
                    $to_load[$field] = $attr;
                }
            }
            foreach ($associations as $k => $v) {
                if (($v['assoc'] == self::JOIN_ONE_TO_ONE || $v['assoc'] == self::JOIN_MANY_TO_ONE) && (!empty($v['auto_load']) and
                    $v['auto_load'] == true)
                ) {
                    $assoc_to_load[] = $k;
                }
            }
        } elseif (is_array($auto_load) || is_string($auto_load)) {
            if (is_string($auto_load)) {
                $auto_load = array($auto_load);
            }

            foreach ($auto_load as $field) {
                if ($field == '*') {
                    foreach (static::getAttributesMap() as $f) {
                        if (!in_array($f, $to_load)) {
                            $to_load[] = $f;
                        }
                    }
                } elseif (static::isMyAttribute($field) and !isset($this->loaded_attributes[self::normalizeAttribute($field)])) {
                    $to_load[] = $field;
                } elseif ($attr = static::isMyDbField($field) and !isset($this->loaded_attributes[$field])) {
                    $to_load[] = $attr;
                } elseif (static::isMyAssociation($field) and !array_key_exists(self::normalizeAttributeName($field),
                    $this->loaded_associations)
                  and in_array($associations[self::normalizeAttributeName($field)]['assoc'], array(
                    self::JOIN_ONE_TO_ONE,
                    self::JOIN_MANY_TO_ONE
                  ))
                ) {
                    $assoc_to_load[] = $field;
                }
            }
        }

        if (!empty($to_load) || !empty($assoc_to_load)) {
            $this->getAssocAndAttr($to_load, $assoc_to_load);
        }
    }

    /**
     * @return array Un array di attributi. key/values mappano i nomi degli attributi con i campi del Database
     *         In caso che sia necessario trattare il campo in modi particolari lato SQL è possibile sostituire il valore
     *         con un array (invece che una stringa) contenenrte le chiavi:
     *          'name': il nome effettivo del campo
     *          'write': l'espressione da usare nelle query SQL di scrittura
     *          'read': l'espressione da usare nelle query SQL di lettura
     *          'after_read': una callback da chiamare sui dati appena letti dal db, il valore di ritorno verrà
     *                      memorizzato nella cache interna dell'oggetto (attenzione al caso di salvataggio dei dati
     *                      su db! Verrà scritto ciò che questa callback ha restituito a meno che non si definisca una
     *                      callback 'write')
     *          'on_get': una callback per manipolare i dati on-the-fly ogni volta che viene il dato viene richiesto
     *                    con una get. Il valore di ritorno non verrà memorizzato nella cache interna dell'oggetto e la
     *                    callback sarà chiamata a ogni singola get
     *          'join': specifica l'utilizzo di una join del modello per questo attributo. Deve essere il nome della tabella
     *                  o il suo alias specificato in @see self::getJoinsMap()
     *          'writable': booleano. Opzionale. Se true indica che il campo è scrivibile, ed è quindi possibile chiamare
     *                      il setAttribute per questo attributo. Se è false il metodo setAttribute fallirà. Default true
     *         Read può essere una callback php che verrà chiamata per ottenere l'espressione SQL
     *         da inserire nella query. Write DEVE essere una callback, se è vuota verrà presa equivalente a read, se non
     *         è una callback verrà usato il nome del campo.
     *         Tutte ricevono come argomenti il nome del campo. write, after_read e on_get ricevono anche il valore
     *         dell'attributo. on_get riceve come terzo parametro $this
     *         write deve ritornare l'espressione del valore del campo già DB::escape()d e inclusa in single quotes se
     *         applicabili, read deve ritornare l'espressione del nome del campo senza nessuna espressione "AS" che verrà
     *         automaticamente aggiunta. after_read dovrebbe ritornare il valore dell'attributo da memorizzare nella cache
     *         interna dell'oggetto
     */
    protected abstract static function getAttributesMap();

    /**
     * @return array Un array di tutte le join usabili in questo Model.
     *        Ogni elemento dell'array ha una chiave che verrà usata come nome
     *          della join (evitare sovrapposizioni di nomi con gli attributi!)
     *          e il suo valore è a sua volta un array contenente:
     *          'type': opzionale. Il tipo di join, può essere INNER, LEFT o RIGHT. Default INNER
     *          'assoc': tipo di associazione. Valori ammessi: OneToOne, OneToMany, ManyToOne, ManyToMany
     *          'helper_table': usato solo per le associazione ManyToMany. Un array contenente le chiavi
     *                    'type': come il 'type' dell'array padre
     *                    'table': li nome della tabella di appoggio da usare per la relazione molti a molti
     *                    'first_field': il campo da usare per la join con il model corrente
     *                    'second_field': il campo da usare per la join con il model da associare
     *                    'condition': una condizione SQL da usare al posto della condizine di default costruita con *_field
     *          'model': l'altra classe del model da collegare o il nome della tabella se non ha un model
     *          'my_field': il nome campo del DB del model corrente da usare nella condizione di join
     *          'its_field': il nome del campo del DB del model da associare, da usare nella condizione di join
     *          'condition': una condizione SQL da usare al posto della condizine di default costruita con *_field.
     *                       ricordarsi di usare le notazioni {ŧable/alias}.{field} se evitare ambiguità
     *          'auto_load': boolean. Se true l'associazione viene caricata automaticamente quando il model viene
     *                       instanziato con il parametro auto_load o con info. I dati in info devono avere nel nome
     *                       il prefisso corrispondente al nome di questa associazione.
     *                       Viene considerato solo per associazioni di tipo OneToOne e ManyToOne.
     */
    protected abstract static function getJoinsMap();

    /**
     * Controlla se una stringa rappresenta il nome di un attributo di questa classe
     * @param $attr : il nome dell'attributo da controllare
     * @return true $attr se è il nome di un attributo valido per la classe. False altrimenti
     */
    protected static function isMyAttribute($attr)
    {
        $attributes = static::getAttributesMap();

        return isset($attributes[self::normalizeAttributeName($attr)]);
    }

    /**
     * Trasformazione per rendere la sintassi dei nomi degli attributi compatibile sia con la forma CamelCase
     * che con quella ad underscore
     * @param string $name : il nome di un attributo della classe
     * @return string il nome dell'attributo normalizzato, come trovabile in static::getAttributesMap()
     */
    protected static function normalizeAttributeName($name)
    {
        return str_replace('_', '', strtolower($name));
    }

    /**
     * Metodo usato per trovare il campo o espressione SQL corrispondente a un attributo della classe
     * @param string $attr : uno dei valori dell'array di static::getAttributesMap()
     * @param mixed $value : Opzionale. Il valore dell'attributo, se applicabile ala modalità.
     * @param string $mode : Opzionale. la modalità di applicazione che mi interessa: read, write o name. name default.
     * @param array $add_opt : array per inserire opzioni aggiuntive. Chiave 'prefix' per inserire un prefisso
     *        prima dei nome dei campi rinominati con AS nelle read. Chiave 'table' per specificare il nome della tabella
     *        in cui si trova il campo
     * @return string In $mode=='name' ritorna il semplice nome del campo sul DB
     *          in $mode=='read' ritorna il nome o l'espressione SQL del campo. In $mode='write'
     *          ritorna un array: nella chiave 0 c'è il nome del campo del DB, nella chiave 1 c'è l'espressione SQL del
     *          valore da dare all'attributo. Ritorna NULL se $attr non viene riconosciuto. I valori sono già passati da
     *          DB::escape()
     * @throws ModelException if the attribute is not found in the current class
     */
    protected static function normalizeAttribute($attr, $mode = 'name', $value = null, $add_opt = array())
    {
        $attributes = static::getAttributesMap();
        $attr = self::normalizeAttributeName($attr);
        if (isset($attributes[$attr])) {
            $a_value = $attributes[$attr];
            $mode = strtolower($mode);

            if (is_array($a_value)) {
                if (empty($a_value['name'])) {
                    return null;
                }

                if (empty($a_value['read'])) {
                    $a_value['read'] = $a_value['name'];
                } elseif (empty($a_value['write'])) {
                    $a_value['write'] = $a_value['name'];
                }
                if (empty($a_value['writable'])) {
                    $a_value['writable'] = true;
                }

                switch ($mode) {
                    case 'name':
                    default:
                        return $a_value['name'];
                    case 'write':
                        if (is_callable($a_value['write'])) {
                            $out = call_user_func($a_value['write'], $a_value['name'], $value);
                        } else {
                            //Nothing...use normal value
                            $out = "'" . Site::DB()->escape($value) . "'";
                        }

                        return array($a_value['name'], $out);
                    case 'after_read':
                        if (!empty($a_value['after_read']) and is_callable($a_value['after_read'])) {
                            return call_user_func($a_value['after_read'], $a_value['name'], $value);
                        } else {
                            return $value;
                        }
                    case 'on_get':
                        if (!empty($a_value['on_get']) and is_callable($a_value['on_get'])) {
                            return call_user_func($a_value['on_get'], $a_value['name'], $value, $add_opt['this']);
                        } else {
                            return $value;
                        }
                    case 'writable':
                        return $a_value['writable'];
                    case 'read':
                        if (is_callable($a_value['read'])) {
                            $out = call_user_func($a_value['read'],
                              (!empty($add_opt['table']) ? $add_opt['table'] . "." : '')
                              . $a_value['name']);
                        } else {
                            $out = (!empty($add_opt['table']) ? $add_opt['table'] . "." : '') . $a_value['read'];
                        }

                        return $out . " AS " . (!empty($add_opt['prefix']) ? $add_opt['prefix'] : '') . $a_value['name'];
                }
            } else {
                switch ($mode) {
                    case 'write':
                        if ($value === null) {
                            return array('`' . $a_value . '`', 'NULL');
                        } else {
                            return array('`' . $a_value . '`', "'" . Site::DB()->escape($value) . "'");
                        }
                    case 'after_read':
                        return $value;
                    case 'on_get':
                        return $value;
                    case 'writable':
                        return true;
                    case 'read':
                        return (!empty($add_opt['table']) ? '`' . $add_opt['table'] . "`." : '') . '`' . $a_value . '`' . (!empty
                        ($add_opt['prefix']) ? " AS " . $add_opt['prefix'] . $a_value : '');
                    case 'name':
                    default://name and read are the same
                        return $a_value;
                }
            }
        }
        throw new ModelException(self::MODEL_NO_ATTR, 1,
          $attr . " non è risconosciuto come attributo di " . get_called_class());
    }

    /**
     * Controlla se una stringa rappresenta il nome di un campo del database legato a questa classe
     * @param $field : il nome del campo da controllare
     * @return bool|string Se il campo appartiene a questa classe ritorna una stringa non vuota che rappresenta
     *                  il nome di uno degli attributi che corrispondono a questo campo. False altrimenti
     */
    protected static function isMyDbField($field)
    {
        $attributes = static::getAttributesMap();
        foreach ($attributes as $attr => $v) {
            if (self::normalizeAttribute($attr, 'name') == $field) {
                return $attr;
            }
        }

        return false;
    }

    protected static function isMyAssociation($name)
    {
        $joins = static::getJoinsMap();
        if (isset($joins[self::normalizeAttributeName($name)])) {
            return true;
        }

        return false;
    }

    /**
     * Prende i valori di associazioni (semplici) e attributi in una sola query
     * @param: $attr: un array contenente nomi di attributi. I nomi possono essere camelCase,
     *                StudlyCaps o con_underscore
     * @param $assoc : un array di nomi di associazioni da caricare. Stessa sintassi degli attributi
     *                usare questo metodo per caricare associazioni che non hanno delle loro classi model
     *                può causare un peggioramento delle prestazioni
     * @param $reload : se true i dati già caricati nell'oggetto vengono ignorati e caricati di nuovo da db. Default false
     * @return array di attributi (SOLO! no associazioni) con i loro valori
     */
    private function getAssocAndAttr($attr, $assoc, $reload = false)
    {
        $out = array();
        $fields_to_attr = array();
        $fields = array();

        if (!empty($attr)) {
            $fields = $this->prepareGetAttributeData($attr, $fields_to_attr, $out, $reload);
        }

        //Add assoc to fields
        $assoc_map = static::getJoinsMap();
        foreach ($assoc as $ass_name) {
            if (static::isMyAssociation($ass_name)) {
                if ($reload || !array_key_exists(self::normalizeAttributeName($ass_name), $this->loaded_associations)) {
                    $data = $assoc_map[self::normalizeAttributeName($ass_name)];
                    $join_info = $this->getJoinTableData($ass_name, $data);
                    $cols = array();
                    if (class_exists($join_info['model']) and method_exists($join_info['model'], 'getAttributesMap')) {
                        $all_att = array_keys($join_info['model']::getAttributesMap());
                        $temp = array();
                        foreach ($all_att as $a) {
                            $temp[] = $join_info['model']::normalizeAttribute($a, 'read', null, array(
                              'prefix' => $join_info['name'] . '_',
                              'table' => $join_info['name']
                            ));
                            $fields_to_attr[$join_info['name'] . '_' . $join_info['model']::normalizeAttribute($a)] = 'JOIN' . $a;
                        }
                        $fields['model_join'][$ass_name] = array_unique($temp);//This to avoid to request the same fields more than once in the query
                    } else {
                        $cols = $this->findTableColumns($data['model']);
                        foreach ($cols as $c) {
                            $fields['model_join'][$ass_name][] = $join_info['name'] . '.' . $c . " AS " . $join_info['name'] . '_' . $c;
                            $fields_to_attr[$join_info['name'] . '_' . $c] = 'JOIN' . $c;
                        }
                    }
                }//No else. Assocs are not in the output
            }
        }

        $values = empty($fields) ? array() : $this->load($fields);
        $assoc_init = array();

        foreach ($values as $field => $value) {
            $corr_attr = $fields_to_attr[$field];
            //Little trick to avoid names clashes between local attributes and assoc attributes/fields
            if (strpos($corr_attr, 'JOIN') === 0) {
                if (!empty($value)) {
                    $corr_attr = substr($corr_attr, 4);
                    $chunk = explode('_', $field);
                    $field_name = (count($chunk) > 2 ? implode('_', array_slice($chunk, 1, null, true)) : $chunk[1]);
                    $assoc_init[$chunk[0]][$field_name] = $value;
                }
            } elseif (static::isMyAttribute($corr_attr)) {
                $this->loaded_attributes[$field] = self::normalizeAttribute($corr_attr, 'after_read', $value);
                $out[$corr_attr] = static::normalizeAttribute($corr_attr, 'on_get', $this->loaded_attributes[$field],
                  array
                  (
                    'this' => $this
                  ));
            }
        }

        foreach ($assoc_init as $ass => $data) {
            $model = $assoc_map[$ass]['model'];
            if (class_exists($model)) {
                $this->loaded_associations[$ass] = $model::buildFromArray($data, true);
            } else {
                $this->loaded_associations[$ass] = new GenericModel($model, $data);
            }
        }

        return $out;
    }

    /**
     * Helper function to process attributes data for $this->getAttribute()
     */
    private function prepareGetAttributeData($input, &$fields_to_attr = array(), &$out = array(), $reload = false)
    {
        $fields = array();
        foreach ($input as $attr) {
            if (static::isMyAttribute($attr)) {
                $field_name = self::normalizeAttribute($attr);
                if ($reload or !isset($this->loaded_attributes[$field_name])) {
                    $fields[] = "THIS." . self::normalizeAttribute($attr, 'read');
                    $fields_to_attr[$field_name] = $attr;//Per ricostruire l'associazione tra nome campo e nome attributo senza ricalcolarla
                } else {
                    $out[$attr] = static::normalizeAttribute($attr, 'on_get', $this->loaded_attributes[$field_name],
                      array
                      (
                        'this' => $this
                      ));
                }
            } else {
                $out[$attr] = null;
            }
        }

        return $fields;
    }

    /**
     * Costruisce i dati necessari per costruire la query SQL delle join.
     * Riconosce in automatico se il modello referenziato esiste o deve essere usata
     * una classe generica
     * @param $nome : il nome dell'associazione
     * @param $j : i dati della join
     * @param $additional_opt : parametri addizionali
     * @return array
     */
    private static function getJoinTableData($nome, $j, $additional_opt = array())
    {
        $output = array('name' => $nome);
        if (class_exists($j['model'])) {
            $output['model'] = $j['model'];
            $output['table'] = $j['model']::getTableName();
            $output['select'] = $j['model']::normalizeAttribute($j['model']::getIdName(), 'read', null, array
            (
              'table' => $output['name']
            ));
        } else {
            $output['model'] = 'GenericModel';
            GenericModel::setNextTableType($j['model']);
            $output['table'] = $j['model'];
            $output['select'] = '`' . $output['name'] . '`.*';
        }
        $output['join'] = self::buildJoinType($j);
        $output['on'] = self::buildSimpleJoinCondition($nome, $j);

        $output['orderby'] = '';
        if (!empty($additional_opt) and !empty($additional_opt['orderby'])) {
            $output['orderby'] = $additional_opt['orderby'];
        } elseif (!empty($j['orderby'])) {
            $output['orderby'] = $j['orderby'];
        }

        return $output;
    }

    /**
     * Utilità per costruire la giusta parola SQL per il tipo di Join
     * @param $join : un array che rappresenta una join
     * @return string INNER, LEFT O RIGHT
     */
    private static function buildJoinType($join)
    {
        if (!empty($join['type']) && in_array(strtoupper($join['type']), array('INNER', 'LEFT', 'RIGHT'))) {
            return strtoupper($join['type']);
        }

        return 'INNER';
    }

    /**
     * Costruisce uno statement che può andare nelle condizioni ON di una Join SQL
     * Genera solo condizioni per join tra due tabelle, non join many to many
     * @param $name : il nome dato all'associazione
     * @param $j : un array che rappresenta una join
     * @return string il codice SQL della condizione di Join
     */
    private static function buildSimpleJoinCondition($name, $j)
    {
        $cond = '';
        if (!empty($j['condition'])) {
            $cond = $j['condition'];
        } else {
            $cond = "THIS.`" . $j['my_field'] . "`=`" . $name . "`.`" . $j['its_field'] . "`";
        }

        return $cond;
    }

    /**
     * Retrieves the columns names from a table of the DB
     * @param $table : the name of the DB table
     * @return array with all the columns names of the table
     *          and the primary key in the 'primary' element.
     *          NULL if the table doesn't exist
     */
    private function findTableColumns($table)
    {
        $columns = array();
        try {
            $cols = Site::DB()->query("SHOW COLUMNS IN `" . Site::DB()->escape($table) . "`");
            while ($col = $cols->fetch()) {
                if ($col['Key'] == 'PRI') {
                    $columns['primary'] = $col['Field'];
                } else {
                    $columns[] = $col['Field'];
                }
            }

            return $columns;
        } catch (DBException $e) {
            return null;
        }
    }

    /**
     * Vera funzione che estrae il valore di un campo dal database
     * @param string $field : il nome del campo o l'espressione SQL da ricercare (un espressione usabile nella clausola SELECT).
     *                Può essere un array di campi o espressioni SQL che verranno inserite separati da virgole;
     *                ogni campo referenziato può (DEVE se sono richieste anche delle associazioni) essere referenziato
     *                nella notazione puntata con il nome della tabella, usare THIS come nome della tabella di questo model.
     *                è possibile richiedere anche dei dati di associazioni (solo OneToOne e ManyToOne) inserendoli nella chiave 'model_join';
     *                ogni elemento di model_join deve essere un array con chiave corrispondente al nome dell'associazione e
     *                valori pari ai nomi dei campi che si vogliono estrarre
     * @return mixed il valore del campo, o, se $field era un array, l'array dei risultati. Null o un array vuoto se il
     * risultato è vuoto
     * @throws ModelException se non è stato possibile caricare il campo. Eventuali errori dal DB sono già memorizzati con watchdog
     */
    private function load($field)
    {
        try {
            if (!empty($field)) {
                $primary_field = self::normalizeAttribute(static::getIdName());
                $primary = self::normalizeAttribute(static::getIdName(), 'write',
                  $this->loaded_attributes[$primary_field]);
                if (is_string($field)) {
                    $q = Site::DB()->query("SELECT " . $field . " FROM " . static::getTableName() . " WHERE " . $primary[0] . "=" . $primary[1],
                      true, DB::RESULT_ARRAY);
                    if (!empty($q)) {
                        return $q[0];
                    } else {
                        return null;
                    }
                } elseif (is_array($field)) {
                    $joins = $this->loadAnalyzeJoins($field);
                    $q = Site::DB()->query("SELECT " . implode(', ',
                        $field) . " FROM " . static::getTableName() . " AS THIS " . implode(' ',
                        $joins) . " WHERE THIS." . $primary[0] . "=" . $primary[1], true);
                    if (!empty($q)) {
                        return $q;
                    } else {
                        return array();
                    }
                }
            }
            throw new ModelException("Il campo non può essere vuoto", 2);
        } catch (DBException $e) {
            $e->logError("Errore loadAttribute di " . get_called_class() . ":");
            throw new ModelException("Non è stato possibile recuperare le informazioni sul campo", 3);
        }
    }

    /**
     * @return string Il nome dell'attributo che rappresenta la chiave primaria della tabella. Supporta solo chiavi primarie a campo singolo
     */
    protected abstract static function getIdName();

    /**
     * @return string Il nome della tabella a cui fare le query
     */
    protected abstract static function getTableName();

    /**
     * Analizza la lista di campi di $this->load() per estrarne i campi delle join e
     * prapararli per l'uso nella query
     * @param $fields : la lista di campi come passata a $this->load(). Alla fine del metodo
     *                  la lista viene modificata con i campi pronti
     * @return array di join da inserire nella query SQL
     */
    private function loadAnalyzeJoins(&$field)
    {
        $joins = array();
        if (isset($field['model_join'])) {
            $model_joins = $field['model_join'];
            $j_map = static::getJoinsMap();
            unset($field['model_join']);
            foreach ($model_joins as $join_name => $join_attr) {
                if (static::isMyAssociation($join_name)) {
                    $join_info = $j_map[self::normalizeAttributeName($join_name)];
                    if ($join_info['assoc'] == self::JOIN_ONE_TO_ONE || $join_info['assoc'] == self::JOIN_MANY_TO_ONE) {
                        $join_data = $this->getJoinTableData($join_name, $join_info);
                        $joins[] = self::buildJoinClause($join_name);
                        if (is_string($join_attr)) {
                            $field[] = $join_attr;
                        } elseif (is_array($join_attr)) {
                            $field = array_merge($field, $join_attr);
                        }
                    }
                }
            }
        }

        return $joins;
    }

    /**
     * Costruisce la parte di comando SQL che fa la join di una associazione
     * @param $join_name : il nome dell'associazione
     * @return string
     */
    private static function buildJoinClause($join_name)
    {
        $joins = static::getJoinsMap();
        $join_name_norm = self::normalizeAttributeName($join_name);
        if (!empty($joins[$join_name_norm])) {
            $raw_data = $joins[$join_name_norm];
            $data = self::getJoinTableData($join_name, $raw_data);
            switch ($raw_data['assoc']) {
                case self::JOIN_ONE_TO_ONE:
                case self::JOIN_MANY_TO_ONE:
                case self::JOIN_ONE_TO_MANY:
                    return ' ' . $data['join'] . ' JOIN ' . $data['table'] . ' AS `' . $join_name . '` ON ' . $data['on'];
                case self::JOIN_MANY_TO_MANY:
                    $clause = ' ' . self::buildJoinType($raw_data['helper_table']) . ' JOIN ' . $raw_data['helper_table']['table'] . ' AS `'
                      . $join_name . 'Helper` ON ';
                    if (empty($raw_data['helper_table']['condition'])) {
                        $clause .= 'THIS.`' . $raw_data['my_field'] . '`=`' . $join_name . 'Helper`.`'
                          . $raw_data['helper_table']['first_field'] . '`';
                    } else {
                        $clause .= $raw_data['helper_table']['condition'];
                    }
                    $clause .= ' ' . $data['join'] . ' JOIN ' . $data['table'] . ' AS `' . $join_name . '` ON ';
                    if (empty($raw_data['condition'])) {
                        $clause .= '`' . $join_name . 'Helper`.`' . $raw_data['helper_table']['second_field'] . "`=`" . $join_name . "`.`"
                          . $raw_data['its_field'] . "`";
                    } else {
                        $clause .= $raw_data['condition'];
                    }

                    return $clause;
            }
        }
    }

    /**
     * Checks if a specific instance of the model exists
     * @param $id : the value of the primary key to look for
     * @return bool
     */
    public static function exists($id)
    {
        $key = self::normalizeAttribute(static::getIdName(), 'write', $id);
        $q = Site::DB()->query("SELECT COUNT(*) AS N FROM " . static::getTableName() . " WHERE " . $key[0] . '=' . $key[1],
          true);

        return $q['N'] > 0;
    }

    /**
     * Finds only one record
     * WARNING: this function overwrites the value of $search['search_limit']
     * @see Model::find()
     * @param array $search
     * @return static the first result in the research
     */
    public static function findOne($search = array())
    {
        $search['search_limit'] = 1;
        $result = self::find($search);
        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * Cerca record nel database
     * @param $search : un array con le condizioni di ricerca.
     *                Le chiavi dell'array possono essere nomi di campi del database o nomi di attributi con la
     *                corrispondenza data in @see static::getAttributesMap(). Se la corrispondenza coinvolge delle
     *                callback, verrà usata quella di scrittura ('write') sul valore da ricercare
     *                Se un elemento corrispondente a una chiave con il nome di un attributo è un array, verrà ricercata
     *                al suo interno la chiave 'op' che indica l'operatore da utilizzare per il confronto (di default viene
     *                usata l'uguaglianza, ma in questo modo è possibile specificare altri operatori) gli altri valori
     *                verranno usati come valori per i confronti messi in OR tra di loro.
     *                Tutte le condizioni sono in AND tra loro a meno che non venga specificata la chiave
     *                'search_op' uguale a 'OR' in tal caso le condizioni saranno in OR tra loro.
     *                Se una chiave è 'search_literal' e l'array contiene solo due elementi (contando anche quello di
     *                search_literal) allora il secondo elemento viene inserito nella query direttamente così come
     *                è stato inserito.
     *                Se una chiave è 'search_joins' il suo valore (o il suo array di valori) verrà interpretato come
     *                le join da agguingere al FROM. Se la chiave di una delle join non è un intero, verrà inserita nel
     *                codice SQL così com'è fornita, così da permettere di fare join arbistrarie e non solo quelle
     *                definite in self::getJoinsMap(); la chiave deve essere il nome dato alla tabella di questa join. I
     *                campi delle join possono essere usati solo nelle condizioni literal e negli ordinamenti, non nella
     *                selezione dei campi da ritornare e neanche nelle condizioni where non literal
     *                L'Array può essere anche multilivello per poter inserire sotto-condizioni mischiando AND e OR,
     *                le chiavi degli elementi multilivello DEVONO essere interi, altrimenti l'elemento verra interpretato
     *                come un attributo da ricercare, ma se non corrisponderà ad alcun attributo verrà ignorato.
     *                Se una chiave è 'search_limit' il suo valore verrà usato per limitare la query (LIMIT),
     *                per fare limit avanzate il valore può essere un array non-associativo a due valori.
     *                Se una chiave è 'search_order' il suo valore verrà usato per impostare la clausola ORDER BY
     *                Se una chiave è search_fields ed è un array, i valori in esso contenuti saranno considerati come
     *                i nomi dei campi o degli attributi da caricare inizialmente negli oggetti, possono anche essere
     *                attributi di associazioni (DEVONO essere specificati nella forma <associazione>.<attributo>)
     * @return static[] di oggetti che rappresentano record che rispettano le condizioni di $search
     */
    public static function find($search = array())
    {
        $joins = '';
        $order_by = '';
        $limit = '';
        $select_what = 'THIS.*';
        if (!empty($search['search_fields'])) {
            if (is_array($search['search_fields'])) {
                $what = array();
                $associations = static::getJoinsMap();
                foreach ($search['search_fields'] as $f) {
                    $orig_f = $f;
                    if ($f == '*') {
                        $what[] = 'THIS.*';
                    } elseif (static::isMyAttribute($f) or $f = static::isMyDbField($f)) {
                        $what[] = self::normalizeAttribute($f, 'read', null, array('table' => 'THIS'));
                    } else {
                        //$f has been overwritten by isMyDbField. Restore it
                        $f = $orig_f;

                        foreach ($associations as $a => $raw_ass_data) {
                            if (($raw_ass_data['assoc'] == self::JOIN_ONE_TO_ONE || $raw_ass_data['assoc'] == self::JOIN_MANY_TO_ONE)) {
                                if (strpos($f, $a . '.') === 0) {
                                    $ass_f = substr($f, strlen($a) + 1);
                                    $ass_data = self::getJoinTableData($a, $raw_ass_data);
                                    if (method_exists($ass_data['model'], 'normalizeAttribute')) {
                                        $what[] = $ass_data['model']::normalizeAttribute($ass_f, 'read', null, array(
                                          'table' => $a,
                                          'prefix' => $a . '_'
                                        ));
                                    } else {
                                        $what[] = $f . ' AS ' . $a . '_' . $ass_f;
                                    }
                                    $joins[$a] = self::buildJoinClause($a);
                                }
                            }
                        }
                    }
                }
                if (!empty($what)) {
                    $primary_field = self::normalizeAttribute(static::getIdName(), 'read', null,
                      array('table' => 'THIS'));
                    if (!in_array($primary_field, $what) and !in_array('THIS.*', $what)) {
                        $what[] = $primary_field;
                    }
                    $select_what = implode(', ', $what);
                }
            }
            unset($search['search_fields']);
        }
        if (!empty($search['search_joins'])) {
            if (is_array($search['search_joins'])) {
                foreach ($search['search_joins'] as $k => $j) {
                    if (is_int($k)) {
                        $joins[$j] = self::buildJoinClause($j);
                    } else {
                        $joins[$k] = ' ' . $j;
                    }
                }
            } else {
                $joins[$search['search_joins']] = self::buildJoinClause($search['search_joins']);
            }
            $joins = implode('', $joins);
            unset($search['search_joins']);
        }
        if (!empty($search['search_limit'])) {
            if (is_array($search['search_limit'])) {
                if (!empty($search['search_limit'][0]) && !empty($search['search_limit'][1])) {
                    $limit = ' LIMIT ' . (int)$search['search_limit'][0] . ',' . $search['search_limit'][1];
                }
            } else {
                $limit = ' LIMIT ' . (int)$search['search_limit'];
            }
            unset($search['search_limit']);
        }
        if (!empty($search['search_order'])) {
            $order_by = ' ORDER BY ' . $search['search_order'];
            unset($search['search_order']);
        }
        $where = self::queryParseConditions($search);
        $query = "SELECT " . $select_what . " FROM " . static::getTableName() . ' AS THIS ' . $joins . (!empty($where) ? " WHERE "
            . $where : '') . $order_by . $limit;
        $obj = Site::DB()->query($query);

        return static::buildMany($obj);
    }

    /**
     * converte un array di condizioni in una stringa SQL da inserire nella condizione WHERE
     */
    private static function queryParseConditions($search)
    {
        $fields = array();
        $op = ' AND ';

        if (count($search) == 2 and !empty($search['search_literal'])) {
            unset($search['search_literal']);
            reset($search);
            $exp = current($search);

            return (string)$exp;
        }

        foreach ($search as $name => $value) {
            if (is_int($name) && is_array($value)) {
                $fields[] = '(' . self::queryParseConditions($value) . ')';
            } elseif ($name == 'search_op') {
                if (strtolower(trim($value)) == 'or') {
                    $op = ' OR ';
                }
            } else {
                if (static::isMyAttribute($name) or $name = static::isMyDbField($name)) {
                    $operatore = '=';
                    if (is_array($value)) {//Controllo sull'operatore
                        if (isset($value['op'])) {
                            if (self::isValidSqlOperator($value['op'])) {
                                $operatore = trim(strtoupper($value['op']));
                            }
                            unset($value['op']);
                        }
                        if (count($value) == 1) {
                            reset($value);
                            $value = current($value);
                        }
                    }

                    if($operatore=='=' && $value===null){
                        $operatore='IS NULL';
                    }

                    if (is_array($value)) {
                        switch ($operatore) {
                            /**
                             * L'uguaglianza multipla può essere meglio espressa con l'IN
                             */
                            case 'IN':
                            case '=':
                                $temp_v = array();
                                foreach ($value as $s_value) {
                                    $w = self::normalizeAttribute($name, 'write', $s_value);
                                    $temp_v[] = $w[1];
                                }
                                $fields[] = $w[0] . " IN (" . implode(', ', $temp_v) . ")";
                                break;
                            case 'IS NULL'://In realtà qui l'array sarebbe vuoto e non ce ne siamo accorti
                            case 'IS NOT NULL':
                                $w = self::normalizeAttribute($name, 'write', '0');
                                $fields[] = $w[0] . " " . $operatore;
                                break;
                            default:
                                $temp_v = array();
                                foreach ($value as $s_value) {
                                    $w = self::normalizeAttribute($name, 'write', $s_value);
                                    $temp_v[] = $w[0] . $operatore . $w[1];
                                }
                                $fields[] = '(' . implode(' OR ', $temp_v) . ')';
                        }
                    } else {
                        $w = self::normalizeAttribute($name, 'write', $value);
                        $fields[] = $w[0] . $operatore . $w[1];
                    }
                }
            }
        }

        return implode($op, $fields);
    }

    //Attributes Utilities

    private static function isValidSqlOperator($op)
    {
        static $operators = array('=', '!=', '<>', 'IN', '<', '<=', '>', '>=', 'NOT IN', 'IS NULL', 'IS NOT NULL');

        return in_array(trim(strtoupper($op)), $operators);
    }

    /**
     * Costruttore statico per costruire tanti oggetti della classe in un colpo solo.
     * Utile per trasformare DbResult con tanti record in una collezione di oggetti
     * @param $data : un istanza di DbResult contenente record dalla tabella static::getTableName()
     * @return static[] un array di oggetti
     */
    public abstract static function buildMany(DbResult $data, $auto_load = false);

    public static function attributeSerialAfterRead($field, $value)
    {
        $un = unserialize($value);
        if ($un !== false) {
            return $un;
        } else {
            return array();
        }
    }

    public static function attributeSerialWrite($field, $value)
    {
        return "'" . Site::DB()->escape(serialize($value)) . "'";
    }

    //Associations Utilities

    /**
     * Special INTERNAL method to build the object from an array of data
     * it is not public because it would allow users to build inconsistent objects
     * @param $data : the array of data to load. It must have DBFields as keys
     * @param $apply_callback : boolean. Indica se è necessario chiamare le procedure
     *                         after_read sui dati passati
     * @return static an object of the current model
     */
    protected abstract static function buildFromArray($data, $apply_callback = false);

    /**
     * Implementa getter e setter generici per tutti gli attributi
     */
    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);
        if ($method == 'get' || $method == 'set') {
            $attr_name = substr($name, 3);
            if (!empty($attr_name)) {
                if ($method == 'get') {
                    if (static::isMyAttribute($attr_name)) {
                        if (isset($arguments[0])) {
                            return $this->getSingleAttribute($attr_name, (bool)$arguments[0]);
                        } else {
                            return $this->getSingleAttribute($attr_name);
                        }
                    } elseif (static::isMyAssociation($attr_name)) {
                        if (isset($arguments[1])) {
                            return $this->getAssociation($attr_name, (array)$arguments[0], (bool)$arguments[1]);
                        } elseif (isset($arguments[0])) {
                            if (is_array($arguments[0])) {
                                return $this->getAssociation($attr_name, $arguments[0]);
                            } else {
                                return $this->getAssociation($attr_name, array(), (bool)$arguments[0]);
                            }
                        } else {
                            return $this->getAssociation($attr_name);
                        }
                    } else {
                        $joins = static::getJoinsMap();
                        $assoc = array();
                        //Come ultima spiaggia vedo se il nome inizia con il nome di una associazione
                        //Se così fosse la restante parte potrebbe essere il nome di un suo attributo
                        foreach ($joins as $k => $v) {
                            //è possibile fare questo ragionamento solo con associazioni che hanno un solo oggetto
                            if ($v['assoc'] == self::JOIN_ONE_TO_ONE || $v['assoc'] == self::JOIN_MANY_TO_ONE) {
                                $attr_name = self::normalizeAttributeName($attr_name);
                                if (strpos($attr_name, $k) === 0) {
                                    $assoc['name'] = $k;
                                    $assoc['attr'] = substr($attr_name, strlen($k));
                                    break;
                                }
                            }
                        }
                        if (!empty($assoc)) {
                            if (isset($arguments[1])) {
                                $data = $this->getAssociation($assoc['name'], (array)$arguments[0],
                                  (bool)$arguments[1]);
                            } elseif (isset($arguments[0])) {
                                if (is_array($arguments[0])) {
                                    $data = $this->getAssociation($assoc['name'], $arguments[0]);
                                } else {
                                    $data = $this->getAssociation($assoc['name'], array(), (bool)$arguments[0]);
                                }
                            } else {
                                $data = $this->getAssociation($assoc['name']);
                            }

                            $method = 'get' . $assoc['attr'];
                            if (($data instanceof Model or $data instanceof GenericModel) and $data->hasAttribute($assoc['attr'])) {
                                return $data->$method();
                            }

                            $jData = $this->getJoinTableData($assoc['name'], $joins[$assoc['name']]);
                            if ($data === null && $jData['join'] != 'INNER') {//Optional relationship, don't throw an exception
                                return null;
                            }
                        }
                    }
                } elseif ($method == 'set') {
                    return $this->setAttribute($attr_name, $arguments[0]);
                }
            }
        }
        throw new ModelException(self::MODEL_NO_ATTR, 1,
          $name . ' non è stato riconosciuto come un getter o setter valido per '
          . get_called_class());
    }

    /**
     * Getter generico di *un singolo* attributo.
     * @param name : il nome di un attributo
     * @param bool $reload : Opzionale. Se true forza il metodo a ricaricare i dati dal database piuttosto che usare
     * quelli già caricati in precedenza. Default false
     * @return mixed il valore dell'attributo ricercato
     * @throws ModelException se viene richiesto un attributo che non esiste
     */
    private function getSingleAttribute($name, $reload = false)
    {
        if (static::isMyAttribute($name)) {
            $field = self::normalizeAttribute($name);
            if (!$reload && isset($this->loaded_attributes[$field])) {
                return static::normalizeAttribute($name, 'on_get', $this->loaded_attributes[$field],
                  array('this' => $this));
            } else {
                $val = $this->load(self::normalizeAttribute($name, 'read'));
                if ($val !== null) {
                    $this->loaded_attributes[$field] = self::normalizeAttribute($name, 'after_read', $val);

                    return static::normalizeAttribute($name, 'on_get', $this->loaded_attributes[$field],
                      array('this' => $this));
                } else {
                    return null;
                }
            }
        } else {
            throw new ModelException(self::MODEL_NO_ATTR, 1,
              $name . ' non risulta essere un attributo di ' . get_called_class());
        }
    }

    /**
     * Restituisce oggetti di modelli associati all'oggetto modello corrente
     * @param string $name : il nome dell'associazione da caricare
     * @param array $options
     * @param bool $reload : se true gli oggetti dell'associazione vengono ricaricati da capo dal DB
     * @return mixed un oggetto o un array di oggetti associati all'oggetto corrente
     * @throws ModelException
     */
    public function getAssociation($name, $options = array(), $reload = false)
    {
        $orig_name = $name;
        $name = self::normalizeAttributeName($name);
        if (!array_key_exists($name, $this->loaded_associations) || $reload) {
            $joins = static::getJoinsMap();
            if (isset($joins[$name])) {
                $j = $joins[$name];
                if (!empty($j['model'])) {
                    $join_table = self::getJoinTableData($orig_name, $j, $options);
                    $my_id = static::getIdName();

                    $where = self::normalizeAttribute($my_id, 'write', $this->getAttribute($my_id));
                    $q = Site::DB()->query("SELECT " . $join_table['select'] . " FROM " . $this->getTableName() . " AS THIS "
                      . self::buildJoinClause($orig_name) . " WHERE THIS." . $where[0] . "=" . $where[1] .
                      (empty($j['condition']) ? ' AND ' . $orig_name . '.`' . $j['its_field'] . '` IS NOT NULL' : '') .
                      (!empty($join_table['orderby']) ? ' ORDER BY ' . $join_table['orderby'] : ''));
                    //IS NOT NULL condition is needed to avoid returning one record with LEFT/RIGHT joins when there are no records joined (it would return one record with all fields to NULL)

                    switch ($j['assoc']) {
                        case self::JOIN_ONE_TO_ONE:
                        case self::JOIN_MANY_TO_ONE:
                        default:
                            $el = $join_table['model']::buildMany($q);
                            $this->loaded_associations[$name] = (!empty($el)) ? $el[0] : null;
                            break;

                        case self::JOIN_ONE_TO_MANY:
                        case self::JOIN_MANY_TO_MANY:
                            $this->loaded_associations[$name] = $join_table['model']::buildMany($q);
                            break;
                    }
                } else {
                    throw new ModelException("Il modello specificato non esiste", 6,
                      $j['model'] . ' non è un modello riconosciuto');
                }
            } else {
                throw new ModelException("L'associazione richiesta non esiste", 5,
                  $name . " non risulta essere un'associazione di " . get_called_class());
            }
        }

        return $this->loaded_associations[$name];
    }

    /**
     * Getter generico per gli attributi. Funziona sia per fare get di un attributo singolo che multipli,
     * ma sarebbe preferibile usare i metodi get<Nome Attributo>() per fare get degli attributi singoli
     * @param $name : il nome di un attributo, o un array contenente nomi di attributi. I nomi possono essere camelCase,
     *                StudlyCaps o con_underscore
     * @param $reload : Opzionale. Se true forza il metodo a ricaricare i dati dal database piuttosto che usare quelli già caricati
     *                 in precedenza. Default false
     * @return mixed Il valore dell'attributo ricercato o un array con i valori cercati.
     *          Se un attributo non esiste viene sostituito con NULL
     */
    public function getAttribute($name, $reload = false)
    {
        if (is_string($name)) {
            return $this->getSingleAttribute($name, $reload);
        } elseif (is_array($name)) {
            return $this->getAssocAndAttr($name, array(), $reload);
        }
    }

    /**
     * Controlla se il modello ha un attributo
     * @param string $name : il nome dell'attributo cercato
     * @return bool true se l'attributo esiste in questo model. false altrimenti
     */
    public static function hasAttribute($name)
    {
        return static::isMyAttribute($name);
    }

    /**
     * Imposta il valore di un attributo. Sarebbe preferibile usare set<NomeAttributo> al posto di questo metodo
     * @param string $name : il nome di un attributo. Non può essere la chiave primaria
     * @param mixed $value : il valore a cui settare il campo, NON deve essere già stato passato in DB::escape() ma deve
     * essere comunque un valore pronto per passarci
     *                o per passare nella sua callback di scrittura (@see self::getAttributesMap() 'write'). La callback viene applicata solo al valore inserito nella query al DB,
     *                non in valore assegnato alla cache interna dell'oggetto è esattamente il valore di $value senza che sia passato attraverso alcuna callback
     * @return bool true se la modifica è andata a buon fine, false altrimenti. Eventuali errori sono memorizzati in
     * watchdog
     * @throws ModelException se $name non è un attributo della classe o se è la chiave primaria
     */
    public function setAttribute($name, $value)
    {
        if (static::isMyAttribute($name)) {
            $field = self::normalizeAttribute($name);
            $primary_field = self::normalizeAttribute(static::getIdName());
            if ($field != $primary_field) {//Non ammettiamo cambi di ID
                if ($this->no_save) {
                    $this->loaded_attributes[$field] = $value;

                    return true;
                } else {
                    try {
                        $ini_val = $value;
                        $set_field = self::normalizeAttribute($name, 'write', $value);
                        $primary = self::normalizeAttribute(static::getIdName(), 'write',
                          $this->loaded_attributes[$primary_field]);
                        $q = Site::DB()->query("UPDATE " . static::getTableName() . " SET " . $set_field[0] . "=" . $set_field[1] . " WHERE " . $primary[0] . "=" . $primary[1]);
                        if (Site::DB()->getLastAffectedRows() > 0) {
                            $this->loaded_attributes[$field] = $ini_val;
                        }

                        return true;
                    } catch (DBException $e) {
                        $e->logError("Errore set_attribute:");

                        return false;
                    }
                }
            } else {
                throw new ModelException("Non è possibile modificare la chiave primaria dell'oggetto", 4, 'Non si può
        modificare la chiave primaria di un model');
            }
        } else {
            throw new ModelException(self::MODEL_NO_ATTR, 1,
              $name . ' non è riconosciuto come attributo di ' . get_called_class());
        }
    }

    /**
     * Imposta il valore di @see $this->no_save per impedire che le modifiche ai valori dell'oggetto vengano veramente scritte sul db
     * @param $value : Opzionale. Se true le modifiche vengono impedite, se false le modifiche vengono sempre riportate anche sul db. Default true
     * NOTA: Al passaggio da true a false le modifiche non ancora copiate NON vengono riportate sul DB, il salvataggio va fatto esplicitamente con @see $this->save()!
     */
    public function setNoSave($value = true)
    {
        $this->no_save = (bool)$value;
    }

    /**
     * Salva tutte le informazioni dell'oggetto sul DB. Utile nel caso di utilizzo di @see $this->setNoSave() o del caricamento dati nuovi dal costruttore
     * @return true se il salvataggio è andato a buon fine. False altrimenti. Eventuali errori sono registrati su watchdog
     */
    public function save()
    {
        $fields = array();
        $primary_field = self::normalizeAttribute(static::getIdName());
        foreach ($this->loaded_attributes as $field => $value) {
            if ($field != $primary_field) {
                $field_info = self::normalizeAttribute(static::isMyDbField($field), 'write', $value);
                $fields[] = $field_info[0] . "=" . $field_info[1];
            }
        }
        try {
            $primary = self::normalizeAttribute(static::getIdName(), 'write', $this->loaded_attributes[$primary_field]);
            Site::DB()->query("UPDATE " . static::getTableName() . " SET " . implode(', ',
                $fields) . " WHERE " . $primary[0] . "=" . $primary[1]);

            return true;
        } catch (DBException $e) {
            $e->logError("Errore aggiornamento oggetto " . get_class($this));

            return false;
        }
    }

    /**
     * Usa le info degli attributi settati in $this per fare una ricerca di utenti nel DB.
     * Tutti gli attributi sono in AND tra di loro eccetto la chiave primaria che NON viene usata per la ricerca
     * @return static[] array di oggetti che rappresentano record che hanno gli stessi attributi dell'oggetto attuale
     */
    public function findLikeMe()
    {
        $primary_field = self::normalizeAttribute(static::getIdName());

        return static::find(array_diff_key($this->loaded_attributes, array($primary_field => 0)));
    }
}
