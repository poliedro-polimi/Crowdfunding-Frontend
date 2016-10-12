<?php
namespace nigiri\models;

use nigiri\db\DbResult;

class Role extends Model{

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
    protected static function getAttributesMap()
    {
        return [
            'name' => 'name',
            'internalname' => 'name',
            'internal' => 'name',
            'display_name' => 'display',
            'display' => 'display'
        ];
    }

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
    protected static function getJoinsMap()
    {
        return [
            'permissions' => [
                'type' => 'LEFT',
                'assoc' => Model::JOIN_ONE_TO_MANY,
                'model' => 'roles_permissions',
                'my_field' => 'name',
                'its_field' => 'role'
            ],
            'users' => [
                'type' => 'LEFT',
                'assoc' => Model::JOIN_ONE_TO_MANY,
                'model' => 'users_roles',
                'my_field' => 'name',
                'its_field' => 'role'
            ]
        ];
    }

    /**
     * @return string Il nome dell'attributo che rappresenta la chiave primaria della tabella. Supporta solo chiavi primarie a campo singolo
     */
    protected static function getIdName()
    {
        return 'name';
    }

    /**
     * @return string Il nome della tabella a cui fare le query
     */
    protected static function getTableName()
    {
        return 'role';
    }

    /**
     * Costruttore statico per costruire tanti oggetti della classe in un colpo solo.
     * Utile per trasformare DbResult con tanti record in una collezione di oggetti
     * @param $data : un istanza di DbResult contenente record dalla tabella static::getTableName()
     * @return static[] un array di oggetti
     */
    public static function buildMany(DbResult $data, $auto_load = false)
    {
        $out = array();
        while ($row = $data->fetch()) {
            if (!empty($row['name'])) {
                $out[] = new Role($auto_load, $row, true);
            }
        }
        return $out;
    }

    /**
     * Special INTERNAL method to build the object from an array of data
     * it is not public because it would allow users to build inconsistent objects
     * @param $data : the array of data to load. It must have DBFields as keys
     * @param bool $apply_callback : boolean. Indica se è necessario chiamare le procedure
     *                         after_read sui dati passati
     * @return static an object of the current model
     * @throws ModelException
     */
    protected static function buildFromArray($data, $apply_callback = false)
    {
        $theId = null;
        foreach ($data as $k => $d) {
            if (static::normalizeAttribute($k, 'name') == 'name') {
                $theId = $k;
                break;
            }
        }

        if (!empty($data[$theId])) {
            return new Role(false, $data, $apply_callback);
        }
        throw new ModelException("ID ruolo mancante nei dati!");
    }
}