<?php


class Booking {

	private $dbh;

	private $bookingsTableName = 'bookings';

	/**
	 * Booking constructor.
	 * @param string $database
	 * @param string $host
	 * @param string $databaseUsername
	 * @param string $databaseUserPassword
	 */
	public function __construct($database, $host, $databaseUsername, $databaseUserPassword) {
		try {

			$this->dbh =
				new PDO(
					sprintf('mysql:host=%s;dbname=%s', $host, $database),
					$databaseUsername,
					$databaseUserPassword
				);
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}

	public function index($cpny_id) {
		$statement = $this->dbh->prepare('SELECT * FROM ' . $this->bookingsTableName . ' WHERE cpny_id = :cpny_id');

		if (false === $statement) {
			throw new Exception('Invalid prepare statement');
		}

		if (false === $statement->execute([':cpny_id' => $cpny_id])) {
			throw new Exception(implode(' ', $statement->errorInfo()));
		} else {
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	public function add(int $cpny_id, DateTimeImmutable $bookingDate, int $status) {
		$statement = $this->dbh->prepare(
			'INSERT INTO ' . $this->bookingsTableName . ' (cpny_id, booking_date, status) VALUES (:cpny_id, :bookingDate, :status)'
		);

		if (false === $statement) {
			throw new Exception('Invalid prepare statement');
		}

		if (false === $statement->execute([
			':cpny_id' => $cpny_id,
			':bookingDate' => $bookingDate->format('Y-m-d'),
			':status' => $status,
		])) {
			throw new Exception(implode(' ', $statement->errorInfo()));
		}
	}

	public function delete($id) {
		$statement = $this->dbh->prepare(
			'DELETE from ' . $this->bookingsTableName . ' WHERE id = :id'
		);
		if (false === $statement) {
			throw new Exception('Invalid prepare statement');
		}
		if (false === $statement->execute([':id' => $id])) {
			throw new Exception(implode(' ', $statement->errorInfo()));
		}
	}
}
