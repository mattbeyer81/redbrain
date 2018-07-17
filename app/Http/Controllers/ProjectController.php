<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class ProjectController extends Controller
{

    /*
    *   Makes a request for all the commits to get details of each commits
    *   Builds array of commits and counts additions and deletions
    *
    *   @param  array   $commits    The commits return from first request to api.github.commit
    *   @param  string  $user       The username of the user of the repo
    *   @param  string  $repo       The name of the repo
    *   @param  Client  $Client     The Guzzle client
    *
    *   @return array
    */

    private function requestCommits(array $commits, string $user, string $repo, Client $client)
    {

        $totalAdditions = 0;
        $totalDeletions = 0;

        $commitsArray = [];

        foreach ($commits as $commit){
            $sha = $commit->sha;

            $url = "https://api.github.com/repos/{$user}/{$repo}/commits/{$sha}";

            $result = $client->get($url, [
                'auth' => [
                    env('GITHUB_USERNAME'),
                    env('GITHUB_PASSWORD')            ]
            ]);
            $res = json_decode($result->getBody()->getContents());

            $stats = $res->stats;
            $additions = $stats->additions;
            $totalAdditions += $additions;

            $deletions = $stats->deletions;
            $totalDeletions += $deletions;

            $commitsArray[] = [
                'sha' => $sha,
                'additions' => $additions,
                'deletions' => $deletions
            ];

        }

        return [
            'total_additions' => $totalAdditions,
            'total_deletions' => $totalDeletions,
            'commits' => $commitsArray
        ];

    }


    /*
    *   Makes request to api.github.com to get commits between dates, for user, repo and author
    *
    *   @param Request $request
    *
    *   $return Response
    */

    public function getCommits(Request $request)
    {
        $client = new Client(['headers' => [
                'Accept' => 'application/vnd.github.cloak-preview',
            ]
        ]);

        $user = $request->get('user');
        $repo = $request->get('repo');
        $from = $request->get('from');
        $to = $request->get('to');
        $authorUsername = $request->get('author-username');

        $url = 'https://api.github.com/';
        $url .= "search/commits?q=";
        $url .= "+committer-date:{$from}..{$to}";
        $url .= "+repo:{$user}/{$repo}";
        $url .= "+author:{$authorUsername}";

        try {
            $result = $client->get($url, [
                'auth' => [
                    env('GITHUB_USERNAME'),
                    env('GITHUB_PASSWORD')            ]
            ]);
            $res = json_decode($result->getBody()->getContents());
            $commits = $this->requestCommits($res->items, $user, $repo, $client);

            return response()->json([
                'user' => $user,
                'repo' => $repo,
                'from' => $from,
                'to' => $to,
                'author-username' => $authorUsername,
                'results' => $commits], 200);

        } catch(GuzzleException $e){
            return response()->json([
                'user' => $user,
                'repo' => $repo,
                'from' => $from,
                'to' => $to,
                'author-username' => $authorUsername,
                'status' => 'fail',
                'message' => $e->getMessage()
            ], 500);
        }


    }

}
