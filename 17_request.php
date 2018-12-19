<?php

class Bomb
{
	private $xBeep;
	private $yBeep;
	private $x1;
	private $y1;
	private $ceil;
	private $floor;

	public function getToken()
	{
		$curl = curl_init();

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL            => "http://exam.osvlabs.com/",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => "",
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => "POST",
			)
		);
		$response = curl_exec($curl);
		curl_close($curl);
		echo $response . "<br>";

		return $response;
	}

	public function detect($token, $x, $y, $hasBeeped = false)
	{
		echo "detector=" . $token . "&x=" . $x . "&y=" . $y;
		$curl = curl_init();

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL            => "http://exam.osvlabs.com/try",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING       => "",
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => "PUT",
				CURLOPT_POSTFIELDS     => "detector=" . $token . "&x=" . $x . "&y=" . $y
			)
		);
		$response = curl_exec($curl);
		curl_close($curl);
		if ($hasBeeped)
		{
			if ($response == "deactivated")
			{
				echo $response . $x . $y . '<br>';
			}
			else
			{
				if (!$this->ceil && !$this->floor)
				{
					$this->x1 = $x + 1;
				}
				if (!$this->ceil)
				{
					$this->ceil = true;
					$this->y1   = ceil(
						sqrt(400 - ($this->x1 - $this->xBeep) * ($this->x1 - $this->xBeep)) + $this->yBeep
					);
					echo $this->x1 . $this->y1 . ' ceil<br><br>';
					$this->detect($token, $this->x1, $this->y1, true);
				}
				else if (!$this->floor)
				{
					$this->floor = true;
					$this->y1    = floor(
						sqrt(400 - ($this->x1 - $this->xBeep) * ($this->x1 - $this->xBeep)) + $this->yBeep
					);
					echo $this->x1 . $this->y1 . ' floor<br><br>';
					$this->detect($token, $this->x1, $this->y1, true);
				}
				else
				{
					$this->ceil  = false;
					$this->floor = false;
					$this->detect($token, $this->x1, 0, true);
				}
			}
		}
		else
		{
			echo $response . "<br>";
			switch ($response)
			{
				case "deactivated":
					echo $response . $x . $y . '<br>';
					break;
				case "beep":
					$this->xBeep = $x;
					$this->yBeep = $y;
					$this->x1    = $x - 20;
					$this->y1    = sqrt(400 - ($this->x1 - $x) * ($this->x1 - $x)) + $y;
					$this->detect($token, $this->x1, $this->y1, true);
					break;
				case "exploded":
					break;
				default:
					if ($x === 20 && $y < 100)
					{
						$y = $y + 1;
						$this->detect($token, $x, $y);
					}
					if ($x === 20 && $y === 100)
					{
						$x = 60;
						$y = - 20;
						$this->detect($token, $x, $y);
					}
					if ($x === 60 && $y < 100)
					{
						$y = $y + 1;
						$this->detect($token, $x, $y);
					}
					if ($x === 60 && $y === 100)
					{
						$x = 100;
						$y = - 20;
						$this->detect($token, $x, $y);
					}
					if ($x === 100 && $y < 100)
					{
						$y = $y + 1;
						$this->detect($token, $x, $y);
					}
			}
		}
	}
}

$bomb = new Bomb();
$bomb->detect($bomb->getToken(), 20, - 20);

?>