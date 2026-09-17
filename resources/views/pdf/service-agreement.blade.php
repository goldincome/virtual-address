<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Virtual Office Service Agreement</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 0;
        }
        .page { padding: 30px; }
        h1 {
            text-align: center;
            font-size: 18px;
            color: #1e3a8a;
            text-transform: uppercase;
            margin: 0 0 18px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
        }
        h2 {
            font-size: 13px;
            color: #1e3a8a;
            text-transform: uppercase;
            margin: 16px 0 6px;
        }
        p { margin: 5px 0; line-height: 1.45; }
        .parties {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px;
        }
        .parties td {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            width: 50%;
        }
        .parties .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }
        .parties .value {
            font-size: 12px;
            font-weight: bold;
        }
        .field-box {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 6px 10px;
            margin: 4px 0;
            min-height: 22px;
            font-weight: bold;
        }
        ul { margin: 4px 0 4px 18px; padding: 0; }
        li { margin: 2px 0; }
        .sign-block {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .sign-block td {
            padding: 14px 10px;
            border-top: 1px solid #9ca3af;
            width: 50%;
        }
        .sign-block .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 40px;
            display: block;
        }
        .dated-line {
            margin-top: 26px;
        }
        .dated-line .score-line {
            border-top: 1px solid #9ca3af;
            width: 45%;
            padding-top: 5px;
            font-size: 10px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="page">
        <h1>Virtual Office Service Agreement</h1>

        <p>
            This is a Virtual Office Agreement ("Agreement") that is between Virtual Office Provider ("the Provider") and a corporation/business ("the Client") incorporated under the law. Provider and Client may be referred to as "the Party" in individual, and "the Parties" in collective. Parties hereby agree as follows:
        </p>

        <table class="parties">
            <tr>
                <td>
                    <span class="label">Provider's Name</span>
                    <span class="value">Charlton Virtual Office</span>
                    <span class="label" style="margin-top:8px;">Provider's Address</span>
                    <span class="value">Unit 6, Block 3, Dockyard Industrial Estate, Charlton, London. SE18 5PQ</span>
                </td>
                <td>
                    <span class="label">Client's Name</span>
                    <span class="value">{{ $clientName }}</span>
                    <span class="label" style="margin-top:8px;">Client's Address</span>
                    <span class="value">{{ $clientAddress }}</span>
                </td>
            </tr>
        </table>

        <h2>Premises and Services</h2>
        <p>
            The Parties have been involved in a leasing/rental agreement. The provider supplies rental Virtual Office Service to the Client. The Client will be using this Virtual Office service for its intended purpose. Special uses should be noticed before the event via email to the other party.
        </p>
        <p>The address of the Virtual Office Space is as following:</p>
        <div class="field-box">Unit 6, Block 3, Dockyard Industrial Estate, Charlton, London. SE18 5PQ</div>
        <p>The Client will be able to use this Virtual Office Address for the following purposes:</p>
        <ul>
            <li>Registered Office Address</li>
            <li>Prestigious Business Address</li>
            <li>Free mail collection</li>
            <li>Discounted Meeting Room Usage</li>
            <li>Discounted Conference Room Usage</li>
        </ul>

        <h2>Storage</h2>
        <p>
            The Client will not store its personal property in the Virtual Office Space. The Provider is not responsible for any loss, stolen, or damaged items left in the Virtual Office Space. The Provider shall enable the office requirements to the Client as additional service to be paid for with a membership discount: Private meeting, video conferencing and boardrooms globally rooms office phones, tables, chairs, wi-fi connection, coworking lounges, secretaries and receptionist on demand.
        </p>

        <h2>Term</h2>
        <p>
            The term of this Agreement ("Term") shall commence on <strong>{{ $agreementDate }}</strong> and run for a <strong>{{ $durationType }}</strong> period expiring on <strong>{{ $expiryDate }}</strong>.
        </p>
        <p>The Term shall automatically renew for the same time period, unless terminated by either of the Parties.</p>

        <h2>Termination</h2>
        <p>In order to terminate this Agreement, either Party is obliged to send a 30-days prior written notice to the other Party.</p>
        <p>This Agreement may be terminated if:</p>
        <ul>
            <li>One of the Parties commits a material breach of any terms of this Agreement that is not capable of remedy within fifteen (15) days, or that should have remedied fifteen (15) days after written notice and was not,</li>
            <li>One of the Parties becomes unable to perform its duties under this Agreement including the payment duty,</li>
            <li>One of the Parties or its employees or agents engage in any conduct prejudicial to the business of the other.</li>
        </ul>
        <p>
            If the Agreement is terminated, the Client shall pay all rental fees incurred prior to the date of termination, regardless of which party terminated or the reason for the termination, except for the fact that the Provider fails to fulfill its services.
        </p>
        <p>
            Any termination under this provision shall not affect the accrued rights or liabilities of either Party under this Agreement or at law shall be without prejudice to any rights or remedies either Party may be entitled to. Any provision or subpart of this Agreement that is meant to continue after termination or come into force at or after termination shall survive.
        </p>

        <h2>Service Charge</h2>
        <p>
            The Client shall pay the stated amount of the service charges to the Provider mentioned in the Agreement. The service charge fee shall be payable in advance of the 7 days business day of every month. If there is an additional fee that the Provider stated in this Agreement, it shall be paid with the service charge.
        </p>
        <p>A late charge in the amount of &pound;9.99 for each Rent payment made more than 24 hours after the date is due.</p>

        <h2>Subletting and Assignment</h2>
        <p>The Client cannot assign this Agreement or sublease all or any part of the Virtual Office Service provisions to a third party.</p>

        <h2>Indemnification</h2>
        <p>
            The Client agrees not to harm the Provider by any claims, or damages unless caused exclusively by the Provider's negligence. The Provider shall not be liable for any damage or injury to the Client that may be caused in the Virtual Office Environment.
        </p>

        <h2>General Provisions</h2>

        <h2>Amendment</h2>
        <p>This Agreement can only be changed or modified with the written consent or permission from both the Provider and the Client.</p>

        <h2>Governing Law</h2>
        <p>
            This Agreement shall be governed under the laws of the England and Wales, and any dispute concerning this agreement shall be exclusively submitted to the courts of England and Wales.
        </p>

        <h2>Severability</h2>
        <p>
            Should there be conflict between any provision of this Agreement and the applicable laws of the England and Wales (the "Law"), the provision shall be held invalid and the remaining provisions in compliance with the Law shall prevail.
        </p>

        <h2>Non-Waiver</h2>
        <p>
            The failure of the Provider to insist upon the strict compliance of the performance of any of the terms, conditions, and covenants hereof shall not be deemed as relinquishment or waiver of any rights or remedy that the Provider may have, nor shall it be construed as waiver of any subsequent breach or default of the terms, conditions, and covenants herein contained. No waiver shall have been deemed waived by the parties unless expressed in writing and duly signed by the waiving party.
        </p>

        <h2>Entire Agreement</h2>
        <p>
            Except as provided in this Agreement, all herein constitutes the consented and agreed covenants and provisions by the parties. Any prior understanding or representation not set forth herein shall not bind either of the Parties.
        </p>

        <table class="sign-block">
            <tr>
                <td><div class="date-line">Date: <strong>{{ $agreementDate }}</strong></div></td>
                <td></td>
            </tr>
            <tr>
                <td class="sign-line">The Client's Signature</td>
                <td class="sign-line">The Provider's Signature</td>
            </tr>
        </table>
    </div>
</body>
</html>