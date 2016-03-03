# edebex-test
It's a test. Really.

## Installation

Clone the repository, then run `composer install`.

## Usage

Create a text file with approval times in a format understood by the DateTime constructor, eg
```
2016-01-01 08:00
2016-02-28 09:00
2016-02-29 11:00
2016-03-04 11:00
```

Then:
```
php schedule.php <approval_times_file> <holidays_ics_file>
```

## Configuration

The default workweek can be overridden in a JSON file. A workweek is an array of days, a day is an array of work blocks, a work block is an object with a "start" and "stop" fields (both times as string, eg "09:00") and a boolean "mail" field. This is the default workweek as JSON:

```
[
	[{"start": "09:00", "stop": "12:00", "mail": false}, {"start": "13:30", "stop": "17:00", "mail": true}],
	[{"start": "09:00", "stop": "12:00", "mail": true}, {"start": "13:30", "stop": "17:00", "mail": true}],
	[{"start": "09:00", "stop": "12:00", "mail": true}, {"start": "13:30", "stop": "17:00", "mail": true}],
	[{"start": "09:00", "stop": "12:00", "mail": true}, {"start": "13:30", "stop": "17:00", "mail": true}],
	[{"start": "09:00", "stop": "12:00", "mail": true}, {"start": "13:30", "stop": "17:00", "mail": false}]
]
```

The workweek definition must start on monday, if monday (or any other day before a workday) isn't a workday simply provide an empty array for this day.
