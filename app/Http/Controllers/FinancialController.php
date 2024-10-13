<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
class FinancialController extends Controller
{
    public function financialUi(){
        return view('index');
    }
    public function financialUpdate(Request $request)
    {
        $country = $request->input('country');
        $year = $request->input('year');

        if ($country == "Ireland") {
            $data = $this->irelandFinancialYear($year, $country);
        return response()->json([
            'success' => true,
            'message' => 'Ireland financial year data retrieved successfully.',
            'data' => $data,
            'country' => $country,
        ]);
        } elseif ($country == "UK") {
            $data = $this->ukFinancialYear($year, $country);
        return response()->json([
            'success' => true,
            'message' => 'UK financial year data retrieved successfully.',
            'data' => $data,
            'country' => $country,
        ]);
        }

        return response()->json(['error' => 'Invalid country'], 400);
    }

    private function irelandFinancialYear($year, $country)
    {
        $ie_starting_month = "01";
        $ie_starting_day = "01";
        $ie_ending_month = "12";
        $ie_ending_day = "31";

        $ie_fin_date_check = "$year-$ie_starting_month-$ie_starting_day";
        $ie_fin_date_lower_check = strtolower(Carbon::parse($ie_fin_date_check)->format('l'));
        return $this->financialYearStartEnd($country, $year, $ie_starting_month, $ie_starting_day, $year, $ie_ending_month, $ie_ending_day, $ie_fin_date_lower_check, null);
    }

    private function ukFinancialYear($year, $country)
    {
        $split = explode('-', $year);
        $uk_starting_year = $split[0];
        $uk_ending_year = $split[1];

        $uk_starting_month = "04";
        $uk_starting_day = "06";
        $uk_ending_month = "04";
        $uk_ending_day = "05";

        $uk_fin_date_check_starting = "$uk_starting_year-$uk_starting_month-$uk_starting_day";
        $uk_fin_date_lower_check_starting = strtolower(Carbon::parse($uk_fin_date_check_starting)->format('l'));

        $uk_fin_date_check_ending = "$uk_ending_year-$uk_ending_month-$uk_ending_day";
        $uk_fin_date_lower_check_ending = strtolower(Carbon::parse($uk_fin_date_check_ending)->format('l'));

        return $this->financialYearStartEnd(
            $country, 
            $uk_starting_year, 
            $uk_starting_month, 
            $uk_starting_day, 
            $uk_ending_year, 
            $uk_ending_month, 
            $uk_ending_day, 
            $uk_fin_date_lower_check_starting, 
            $uk_fin_date_lower_check_ending
        );
    }

    private function financialYearStartEnd($country, $starting_year, $starting_month, $starting_day, $ending_year, $ending_month, $ending_day, $fin_date_lower_check_starting, $fin_date_lower_check_ending)
    {
        $start = $this->findWeekend($starting_year, $starting_month, $starting_day, true);
        $end = $this->findWeekend($ending_year, $ending_month, $ending_day, false);

        $formattedStartDate = Carbon::parse($start['date'])->format('jS F Y');
        $formattedEndDate = Carbon::parse($end['date'])->format('jS F Y');

        // Check if any weekend days were found
        $date_sat_textual_format = $start['saturday'] ?? "null";
        $date_sun_textual_format = $start['sunday'] ?? "null";
        $end_date_sat_textual_format = $end['saturday'] ?? "null";
        $end_date_sun_textual_format = $end['sunday'] ?? "null";

        if ($country == "Ireland") {
            if ($fin_date_lower_check_starting == "sunday") {
                $date_sat_textual_format = "null";
            }
            if ($fin_date_lower_check_starting == "saturday") {
                $end_date_sun_textual_format = "null";
            }
        }

        if ($country == "UK") {
            if ($fin_date_lower_check_starting == "sunday") {
                $date_sat_textual_format = "null";
            }
            if ($fin_date_lower_check_ending == "saturday") {
                $end_date_sun_textual_format = "null";
            }
        }

        return "$formattedStartDate#$formattedEndDate#$date_sat_textual_format#$date_sun_textual_format#$end_date_sat_textual_format#$end_date_sun_textual_format";
    }

    private function findWeekend($year, $month, $day, $isStart = true)
    {
        $counter = 0;
        $weekendData = [];

        while ($counter < 2) {
            $date = "$year-$month-$day";
            $carbonDate = Carbon::parse($date);
            $dayOfWeek = strtolower($carbonDate->format('l'));

            if ($dayOfWeek === 'saturday' || $dayOfWeek === 'sunday') {
                if ($dayOfWeek === 'saturday') {
                    $weekendData['saturday'] = $carbonDate->format('jS M Y');
                }
                if ($dayOfWeek === 'sunday') {
                    $weekendData['sunday'] = $carbonDate->format('jS M Y');
                }
                $counter++;
            }

            // Adjust day for next loop
            $day = $isStart ? $day + 1 : $day - 1;
        }

        $weekendData['date'] = "$year-$month-$day";
        return $weekendData;
    }

    public function financialHolidays($year, $country){
        $parts = explode(' ', $year);
        $yearOnly = end($parts);
        $client = new Client();
        try {
            $response = $client->get("https://date.nager.at/api/v2/PublicHolidays/{$yearOnly}/{$country}");
            $holidays = json_decode($response->getBody()->getContents(), true);
            return view('financial-holidays', compact('holidays'));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return redirect()->back()->withErrors(['error' => 'Unable to fetch holidays. Please check the country code and try again.']);
        }
    }
}
