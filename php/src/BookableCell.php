<?php

class BookableCell {
	/**
	 * @var Booking
	 */
	private $cpny_id;
	private $booking;

	private $currentURL;

	/**
	 * BookableCell constructor.
	 * @param $booking
	 */
	public function __construct(Booking $booking) {
		$this->cpny_id = htmlentities($_SESSION['cpny_id']);
		$this->booking = $booking;
		$this->currentURL = htmlentities($_SERVER['REQUEST_URI']);
	}

	public function update(Calendar $cal) {
		if ($this->isDateBooked($cal->getCurrentDate()) !== false) {
			// $status = $this->bookedDates();
			// echo "<pre>";
			// var_dump($status);
			// echo "</pre>";
			return $cal->cellContent =
				$this->bookedCell($cal->getCurrentDate(), 0);
		}

		if ($this->isDateBooked($cal->getCurrentDate()) === false) {
			return $cal->cellContent =
				$this->unsetCell($cal->getCurrentDate());
		}

		// if (!$this->isDateBooked($cal->getCurrentDate())) {
		// 	return $cal->cellContent =
		// 		$this->pendingCell($cal->getCurrentDate());
		// }
	}

	public function routeActions() {
		if (isset($_POST['delete'])) {
			$this->deleteBooking($_POST['id']);
		}

		if (isset($_POST['add'])) {
			$this->addBooking($this->cpny_id, $_POST['date'], $_POST['status']);
		}
	}

	private function unsetCell($date) {
		$day = date('j', strtotime($date));
		return '<div class="unset">' .
						'<div class="day">' . $day . '</div>' . $this->bookingForm($date) .
						'</div>';
	}

	private function bookedCell($date, $status) {
		$day = date('j', strtotime($date));
		switch ($status) {
			case 1:
				$booked_status = "open";
				break;
			case 2:
				$booked_status = "booked";
				break;
			case 3:
				$booked_status = "pending";
				break;
			case 4:
				$booked_status = "unavailable";
				break;

			default:
				$booked_status = "unset";
				break;
		}

		return '<div class="' . $booked_status . '">' .
						'<div class="day">' . $day . '</div>' . $this->deleteForm($this->bookingId($date)) .
						'</div>';
	}

	private function isDateBooked($date) {
		// echo "<pre>";
		// var_dump(array_search($date, array_column($this->bookedDates(), 0)));
		// echo "</pre>";
		echo "<pre>";
		var_dump($this->bookedDates());
		echo "</pre>";
		return array_search($date, array_column($this->bookedDates(), 0));
		// return array_search($date, array_column($this->bookedDates(), 0));
		return in_array($date, $this->bookedDates());
	}

	private function bookedDates() {
		$cpny_id = $_GET['cpny_id'];
		return array_map(function ($record) {
			return array($record['booking_date'], $record['status']);
		}, $this->booking->index($cpny_id));
	}

	private function bookingId($date) {
		$booking = array_filter($this->booking->index($this->cpny_id), function ($record) use ($date) {
			return $record['booking_date'] == $date;
		});

		$result = array_shift($booking);

		return $result['id'];
	}

	private function deleteBooking($id) {
		$this->booking->delete($id);
	}

	private function addBooking($cpny_id, $date, $status) {
		$cpny_id = (int)$cpny_id;
		$date = new DateTimeImmutable($date);
		$status = (int)$status;
		$this->booking->add($cpny_id, $date, $status);
	}

	private function bookingForm($date) {
		return
			'<form method="post" action="' . $this->currentURL . '">' .
			'<input type="hidden" name="add" />' .
			'<input type="hidden" name="date" value="' . $date . '" />' .
			'<select name="status" class="status">' .
			'<option value=1>空きあり</option>' .
			'<option value=2>空きなし</option>' .
			'<option value=3>要相談</option>' .
			'<option value=4>営業時間外</option>' .
			'</select>' .
			'<input class="submit" type="submit" value="Book" />' .
			'</form>';
	}

	private function deleteForm($id) {
		return
			'<form onsubmit="return confirm(\'Are you sure to cancel?\');" method="post" action="' . $this->currentURL . '">' .
			'<input type="hidden" name="delete" />' .
			'<input type="hidden" name="id" value="' . $id . '" />' .
			'<input class="submit" type="submit" value="Delete" />' .
			'</form>';
	}
}
