<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\AdsApi\Examples\Dfp\v201702\ProposalService;

require __DIR__ . '/../../../../vendor/autoload.php';

use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\Dfp\DfpServices;
use Google\AdsApi\Dfp\DfpSession;
use Google\AdsApi\Dfp\DfpSessionBuilder;
use Google\AdsApi\Dfp\Util\v201702\DfpDateTimes;
use Google\AdsApi\Dfp\Util\v201702\StatementBuilder;
use Google\AdsApi\Dfp\v201702\ProposalService;

/**
 * This example gets the Marketplace comments for a programmatic proposal.
 *
 * <p>It is meant to be run from a command line (not as a webpage) and requires
 * that you've setup an `adsapi_php.ini` file in your home directory with your
 * API credentials and settings. See README.md for more info.
 */
class GetMarketplaceComments {

  const PROPOSAL_ID = 'INSERT_PROPOSAL_ID_HERE';

  public static function runExample(DfpServices $dfpServices,
      DfpSession $session, $proposalId) {
    $proposalService = $dfpServices->get($session, ProposalService::class);

    // Create a statement to select marketplace comments.
    $statementBuilder = (new StatementBuilder())
        ->where('proposalId = :proposalId')
        ->withBindVariableValue('proposalId', $proposalId);

    $totalResultSetSize = 0;
    $page = $proposalService->getMarketplaceCommentsByStatement(
        $statementBuilder->toStatement());

    if ($page->getResults() !== null) {
      $totalResultSetSize = count($page->getResults());
      foreach ($page->getResults() as $i => $marketplaceComment) {
        printf(
            "%d) Marketplace comment with creation time '%s' and comment "
                . "'%s' was found.\n",
            $i,
            DfpDateTimes::toDateTimeString(
                $marketplaceComment->getCreationTime()),
            $marketplaceComment->getComment()
        );
      }
    }

    printf("Number of results found: %d\n", $totalResultSetSize);
  }

  public static function main() {
    // Generate a refreshable OAuth2 credential for authentication.
    $oAuth2Credential = (new OAuth2TokenBuilder())
        ->fromFile()
        ->build();

    // Construct an API session configured from a properties file and the OAuth2
    // credentials above.
    $session = (new DfpSessionBuilder())
        ->fromFile()
        ->withOAuth2Credential($oAuth2Credential)
        ->build();

    self::runExample(new DfpServices(), $session, intval(self::PROPOSAL_ID));
  }
}

GetMarketplaceComments::main();
