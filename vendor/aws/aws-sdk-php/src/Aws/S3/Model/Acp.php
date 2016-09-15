<?php
/**
 * Copyright 2010-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Aws\S3\Model;

use Aws\Common\Exception\InvalidArgumentException;
use Aws\Common\Exception\OverflowException;
use Guzzle\Common\ToArrayInterface;
use Guzzle\Service\Command\AbstractCommand;

/**
 * Amazon S3 Access Control Policy (ACP)
 */
class Acp implements ToArrayInterface, \IteratorAggregate, \Countable
{
    /**
     * @var \SplObjectStorage List of grants on the ACP
     */
    protected $grants = array();

    /**
     * @var Grantee The user of the ACP
     */
    protected $user;

    /**
     * Constructs an ACP
     *
     * @param Grantee            $user  ACP policy user
     * @param array|\Traversable $grants List of grants for the ACP
     */
    public function __construct(Grantee $user, $grants = null)
    {
        $this->setUser($user);
        $this->setGrants($grants);
    }

    /**
     * Create an Acp object from an array. This can be used to create an ACP from a response to a GetObject/Bucket ACL
     * operation.
     *
     * @param array $data Array of ACP data
     *
     * @return Acp
     */
    public static function fromArray(array $data)
    {
        $builder = new AcpBuilder();
        $builder->setUser((string) $data['User']['ID'], $data['User']['DisplayName']);

        // Add each Grantee to the ACP
        foreach ($data['Grants'] as $grant) {
            $permission = $grant['Permission'];

            // Determine the type for response bodies that are missing the Type parameter
            if (!isset($grant['Grantee']['Type'])) {
                if (isset($grant['Grantee']['ID'])) {
                    $grant['Grantee']['Type'] = 'CanonicalUser';
                } elseif (isset($grant['Grantee']['URI'])) {
                    $grant['Grantee']['Type'] = 'Group';
                } else {
                    $grant['Grantee']['Type'] = 'AmazonCustomerByEmail';
                }
            }

            switch ($grant['Grantee']['Type']) {
                case 'Group':
                    $builder->addGrantForGroup($permission, $grant['Grantee']['URI']);
                    break;
                case 'AmazonCustomerByEmail':
                    $builder->addGrantForEmail($permission, $grant['Grantee']['EmailAddress']);
                    break;
                case 'CanonicalUser':
                    $builder->addGrantForUser(
                        $permission,
                        $grant['Grantee']['ID'],
                        $grant['Grantee']['DisplayName']
                    );
            }
        }

        return $builder->build();
    }

    /**
     * Set the user of the ACP policy
     *
     * @param Grantee $user ACP policy user
     *
     * @return $this
     *
     * @throws InvalidArgumentException if the grantee does not have an ID set
     */
    public function setUser(Grantee $user)
    {
        if (!$user->isCanonicalUser()) {
            throw new InvalidArgumentException('The user must have an ID set.');
        }

        $this->user = $user;

        return $this;
    }

    /**
     * Get the user of the ACP policy
     *
     * @return Grantee
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the grants for the ACP
     *
     * @param array|\Traversable $grants List of grants for the ACP
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setGrants($grants = array())
    {
        $this->grants = new \SplObjectStorage();

        if ($grants) {
            if (is_array($grants) || $grants instanceof \Traversable) {
                /** @var Grant $grant */
                foreach ($grants as $grant) {
                    $this->addGrant($grant);
                }
            } else {
                throw new InvalidArgumentException('Grants must be passed in as an array or Traversable object.');
            }
        }

        return $this;
    }

    /**
     * Get all of the grants
     *
     * @return \SplObjectStorage
     */
    public function getGrants()
    {
        return $this->grants;
    }

    /**
     * Add a Grant
     *
     * @param Grant $grant Grant to add
     *
     * @return $this
     */
    public function addGrant(Grant $grant)
    {
        if (count($this->grants) < 100) {
            $this->grants->attach($grant);
        } else {
            throw new OverflowException('An ACP may contain up to 100 grants.');
        }

        return $this;
    }

    /**
     * Get the total number of attributes
     *
     * @return int
     */
    public function count()
    {
        return count($this->grants);
    }

    /**
     * Returns the grants for iteration
     *
     * @return \SplObjectStorage
     */
    public function getIterator()
    {
        return $this->grants;
    }

    /**
     * Applies grant headers to a command's parameters
     *
     * @param AbstractCommand $command Command to be updated
     *
     * @return $this
     */
    public function updateCommand(AbstractCommand $command)
    {
        $parameters = array();
        foreach ($this->grants as $grant) {
            /** @var Grant $grant */
            $parameters = array_merge_recursive($parameters, $grant->getParameterArray());
        }

        foreach ($parameters as $name => $values) {
            $command->set($name, implode(', ', (array) $values));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $grants = array();
        foreach ($this->grants as $grant) {
            $grants[] = $grant->toArray();
        }

        return array(
            'User' => array(
                'ID'          => $this->user->getId(),
                'DisplayName' => $this->user->getDisplayName()
            ),
            'Grants' => $grants
        );
    }
}
