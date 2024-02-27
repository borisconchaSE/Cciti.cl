<?php

namespace Intouch\Framework\Dao {

    use \PDO;

    class ExecuteDao
    {
        private static $isTransactionOpened = false;

        public static function IsTransactionOpen() {
            return self::$isTransactionOpened;
        }

        public static function OpenTransaction($domain) {

            $conn = DataConfig::GetPDOConnection($domain);

            if (!self::$isTransactionOpened) {
                if ($conn->beginTransaction()) {
                    self::$isTransactionOpened = true;
                    return $conn;
                }
            }
            else {
                return null;
            }
        }

        public static function CommitTransaction($transaction) {
            if ($transaction->commit()) {
                $transaction = null;
                self::$isTransactionOpened = false;
            }
        }

        public static function RollbackTransaction($transaction) {
            if ($transaction->rollback()) {
                $transaction = null;
                self::$isTransactionOpened = false;
            }
        }

        public function ExecuteStatement($stmt, $fetchStyle = \PDO::FETCH_ASSOC)
        {
            $retorno = $stmt->execute();
            if (!$retorno)
                return null;
            else
                return $stmt->fetchAll($fetchStyle);
        }

        public function ExecuteStatementForObject($stmt)
        {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $this->EntityDefinition->Entity);
            $stmt->execute();
            $retorno = $stmt->fetchObject($this->EntityDefinition->Entity);
            if (!$retorno)
                return null;
            else
                return $retorno;
        }

        public function ExecuteStatementForObjects($stmt)
        {

            $stmt->setFetchMode(PDO::FETCH_CLASS, $this->EntityDefinition->Entity);
            $stmt->execute();
            $retorno = $stmt->fetchAll(PDO::FETCH_CLASS, $this->EntityDefinition->Entity);

            if (!$retorno)
                return null;
            else
                return $retorno;
        }

        public function ExecuteStatementForQueryable($stmt)
        {

            $stmt->setFetchMode(PDO::FETCH_OBJ);
            $stmt->execute();
            $retorno = $stmt->fetchAll();

            if (!$retorno)
                return null;
            else
                return $retorno;
        }

        public function ExecuteScalar($stmt) {
            $stmt->execute();
            return $stmt->fetchColumn();
        }
    }
}
