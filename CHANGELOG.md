# Changelog

All notable changes to `laravel-prometheus-exporter` will be documented in this file.

Everything in v1 is subject to breaking changes while we work out the kinks.

## 1.3.0 - 2021-04-21

- use our own pushedAt timestamp via Cache so as not to rely on horizon
- normalize route parameters so each new value is not considered a separate route

## 1.2.0 - 2021-04-16

- added job wait time exporter
- renamed job duration exporter class to job process time exporter

## 1.1.0 - 2021-04-14

- renamed metrics to comply with prometheus best practices
- added ability to opt out of tracking certain queries and paths

## 1.0.1 - 2021-04-02

- actual initial release ;)

## 1.0.0 - 2021-04-02

- initial release
